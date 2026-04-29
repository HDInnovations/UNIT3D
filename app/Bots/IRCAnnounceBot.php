<?php

declare(strict_types=1);

/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Bots;

use Illuminate\Support\Facades\Log;

class IRCAnnounceBot
{
    /**
     * @var resource|null
     */
    private $socket = null;

    private const RPL_WELCOME = 001;

    private string $recipient;

    private bool $isInChannel = false;

    public function __construct()
    {
        $socket = fsockopen(config('irc-bot.server'), config('irc-bot.port'), $_, $_, 5);

        if (!\is_resource($socket)) {
            return;
        }

        $this->socket = $socket;
        $this->nick(config('irc-bot.username'));
        $this->user(config('irc-bot.username'), config('irc-bot.hostname'), config('irc-bot.server'), config('irc-bot.username'));

        $this->connect();

        if (config('irc-bot.nickservpass')) {
            $this->authenticate(config('irc-bot.username'), config('irc-bot.password'));
        }
    }

    public function __destruct()
    {
        sleep(2);

        if ($this->isInChannel) {
            $this->part($this->recipient);
        }

        $this->quit();

        if (\is_resource($this->socket)) {
            fclose($this->socket);
        }
    }

    private function connect(): void
    {
        while (\is_resource($this->socket) && $message = fgets($this->socket)) {
            flush();

            if ($message[0] === ':') {
                [, $command, $parameters] = preg_split('/ +/', $message, 3) + [null, null, null];
            } else {
                [$command, $parameters] = preg_split('/ +/', $message, 3) + [null, null];
            }

            switch ($command) {
                case 'PING':
                    if ($parameters === null) {
                        break;
                    }

                    [$server1] = preg_split('/ +/', (string) $parameters) + [null];

                    if ($server1 === null) {
                        break;
                    }

                    $this->pong($server1);

                    break;
                case self::RPL_WELCOME:
                    // We have successfully connected
                    break 2;
            }
        }
    }

    private function authenticate(string $username, string $password): void
    {
        $this->privmsg('NickServ', "RECOVER {$username} {$password}");
        $this->nick($username);
        $this->privmsg('NickServ', "IDENTIFY {$password}");
    }

    private function send(string $data): void
    {
        if (! \is_resource($this->socket)) {
            Log::error('Tried to send data while not connected.');

            return;
        }

        $bytesWritten = @fwrite($this->socket, $data."\r\n");

        if ($bytesWritten === false) {
            Log::warning('Failed to send IRC data, closing stale socket.');
            fclose($this->socket);
            $this->socket = null;
        }
    }

    public function to(string $recipient): self
    {
        $this->recipient = $recipient;
        $channelKey = config('irc-bot.channel_key', '');

        if (config('irc-bot.joinchannel')) {
            if ($this->isValidChannelName($recipient)) {
                $this->join($recipient, $channelKey);
                $this->isInChannel = true;
            } else {
                Log::error('Tried to channel with invalid name.', [
                    'name' => $this->recipient,
                ]);
            }
        }

        return $this;
    }

    public function say(string $message): self
    {
        if (! isset($this->recipient)) {
            Log::error('Tried to say a message without specifying a recipient.');

            return $this;
        }

        $this->privmsg($this->recipient, $message);

        return $this;
    }

    /**
     * @see https://www.rfc-editor.org/rfc/rfc1459#section-2.3.1
     */
    private function isValidChannelName(string $channel): bool
    {
        // Channel must
        return
            // Length of the channel including the `#` or `&` must be at least 2
            \strlen($channel) >= 2
            // Channel name must begin with either `#` or `&`
            && \in_array($channel[0], ['#', '&'], true)
            // Channel names can contain any 8bit code except for SPACE, BELL, NUL, CR, LF and comma
            && !str_contains($channel, ' ')
            && !str_contains($channel, "\7")
            && !str_contains($channel, "\0")
            && !str_contains($channel, "\r")
            && !str_contains($channel, "\n")
            && !str_contains($channel, ',');
    }

    /**
     * @see https://www.rfc-editor.org/rfc/rfc1459#section-4.1.2
     */
    private function nick(string $nickname): void
    {
        $this->send("NICK {$nickname}");
    }

    /**
     * @see https://www.rfc-editor.org/rfc/rfc1459#section-4.1.6
     */
    private function quit(string $message = ''): void
    {
        $this->send("QUIT {$message}");
    }

    /**
     * @see https://www.rfc-editor.org/rfc/rfc1459#section-4.1.3
     */
    private function user(string $username, string $hostname, string $servername, string $realname): void
    {
        $this->send("USER {$username} {$hostname} {$servername} {$realname}");
    }

    /**
     * @see https://www.rfc-editor.org/rfc/rfc1459#section-4.2.1
     */
    private function join(string $channel, string $channelKey = ''): void
    {
        if (!$this->isValidChannelName($channel)) {
            Log::error('Tried to join a channel with invalid name.', ['name' => $channel]);

            return;
        }

        $this->send("JOIN {$channel} {$channelKey}");
    }

    /**
     * @see https://www.rfc-editor.org/rfc/rfc1459#section-4.2.2
     */
    private function part(string $channel): void
    {
        if (!$this->isValidChannelName($channel)) {
            Log::error('Tried to part a channel with invalid name.', ['name' => $channel]);

            return;
        }

        $this->send("PART {$channel}");
    }

    /**
     * @see https://www.rfc-editor.org/rfc/rfc1459#section-4.4.1
     */
    private function privmsg(string $receiver, string $textToBeSent): void
    {
        $this->send("PRIVMSG {$receiver} :{$textToBeSent}");
    }

    /**
     * @see https://www.rfc-editor.org/rfc/rfc1459#section-4.6.3
     */
    private function pong(string $daemon): void
    {
        $this->send("PONG {$daemon}");
    }
}

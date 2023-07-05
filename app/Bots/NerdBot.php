<?php
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

use App\Events\CreateMessage;
use App\Models\Ban;
use App\Models\Bot;
use App\Models\Message;
use App\Models\Peer;
use App\Models\Torrent;
use App\Models\User;
use App\Models\Warning;
use Illuminate\Support\Carbon;

class NerdBot
{
    private Bot $bot;

    private Carbon $expiresAt;

    private Carbon $current;

    private string $site;

    public function __construct()
    {
        $this->bot = Bot::findOrFail(2);
        $this->expiresAt = Carbon::now()->addMinutes(60);
        $this->current = Carbon::now();
        $this->site = config('other.title');
    }

    public function replaceVars(string $output): string
    {
        $output = str_replace(['{me}', '{command}'], [$this->bot->name, $this->bot->command], $output);

        if (str_contains((string) $output, '{bots}')) {
            $botHelp = '';
            $bots = Bot::query()
                ->where('active', '=', 1)
                ->where('id', '!=', $this->bot->id)
                ->orderByDesc('position')
                ->get();

            foreach ($bots as $bot) {
                $botHelp .= '( ! | / | @)'.$bot->command.' help triggers help file for '.$bot->name."\n";
            }

            $output = str_replace('{bots}', $botHelp, $output);
        }

        return $output;
    }

    public function getBanker(): string
    {
        $banker = cache()->remember(
            'nerdbot-banker',
            $this->expiresAt,
            fn () => User::orderByDesc('seedbonus')->first()
        );

        return "Currently [url=/users/{$banker->username}]{$banker->username}[/url] is the top BON holder on {$this->site}!";
    }

    public function getSnatched(): string
    {
        $snatched = cache()->remember(
            'nerdbot-snatched',
            $this->expiresAt,
            fn () => Torrent::orderByDesc('times_completed')->first()
        );

        return "Currently [url=/torrents/{$snatched->id}]{$snatched->name}[/url] is the most snatched torrent on {$this->site}!";
    }

    public function getLeeched(): string
    {
        $leeched = cache()->remember(
            'nerdbot-leeched',
            $this->expiresAt,
            fn () => Torrent::orderByDesc('leechers')->first()
        );

        return "Currently [url=/torrents/{$leeched->id}]{$leeched->name}[/url] is the most leeched torrent on {$this->site}!";
    }

    public function getSeeded(): string
    {
        $seeded = cache()->remember(
            'nerdbot-seeded',
            $this->expiresAt,
            fn () => Torrent::orderByDesc('seeders')->first()
        );

        return "Currently [url=/torrents/{$seeded->id}]{$seeded->name}[/url] is the most seeded torrent on {$this->site}!";
    }

    public function getFreeleech(): string
    {
        $freeleech = cache()->remember(
            'nerdbot-freeleech',
            $this->expiresAt,
            fn () => Torrent::where('free', '=', 1)->count()
        );

        return "There Are Currently {$freeleech} Freeleech Torrents On {$this->site}!";
    }

    public function getDoubleUpload(): string
    {
        $doubleUpload = cache()->remember(
            'nerdbot-doubleupload',
            $this->expiresAt,
            fn () => Torrent::where('doubleup', '=', 1)->count()
        );

        return "There Are Currently {$doubleUpload} Double Upload Torrents On {$this->site}!";
    }

    public function getPeers(): string
    {
        $peers = cache()->remember(
            'nerdbot-peers',
            $this->expiresAt,
            fn () => Peer::where('active', '=', 1)->count()
        );

        return "Currently There Are {$peers} Peers On {$this->site}!";
    }

    public function getBans(): string
    {
        $bans = cache()->remember(
            'nerdbot-bans',
            $this->expiresAt,
            fn () => Ban::whereNull('unban_reason')
                ->whereNull('removed_at')
                ->where('created_at', '>', $this->current->subDay())->count()
        );

        return "In the last 24 hours, {$bans} users have been banned from {$this->site}";
    }

    public function getWarnings(): string
    {
        $warnings = cache()->remember(
            'nerdbot-warnings',
            $this->expiresAt,
            fn () => Warning::where('created_at', '>', $this->current->subDay())->count()
        );

        return "In the last 24 hours {$warnings} hit and run warnings have been issued on {$this->site}!";
    }

    public function getUploads(): string
    {
        $uploads = cache()->remember(
            'nerdbot-uploads',
            $this->expiresAt,
            fn () => Torrent::where('created_at', '>', $this->current->subDay())->count()
        );

        return "In the last 24 hours {$uploads} torrents have been uploaded to {$this->site}!";
    }

    public function getLogins(): string
    {
        $logins = cache()->remember(
            'nerdbot-logins',
            $this->expiresAt,
            fn () => User::whereNotNull('last_login')->where('last_login', '>', $this->current->subDay())->count()
        );

        return "In The Last 24 Hours {$logins} Unique Users Have Logged Into {$this->site}!";
    }

    public function getRegistrations(): string
    {
        $registrations = cache()->remember(
            'nerdbot-users',
            $this->expiresAt,
            fn () => User::where('created_at', '>', $this->current->subDay())->count()
        );

        return "In the last 24 hours {$registrations} users have registered to {$this->site}!";
    }

    public function getHelp(): string
    {
        return $this->replaceVars($this->bot->help);
    }

    public function getKing(): string
    {
        return "{$this->site} is king!";
    }

    /**
     * Handle command.
     *
     * @param array<string, ?int> $echo
     */
    public function handle(?string $command, array $echo = []): void
    {
        $message = Message::create([
            'user_id'     => User::SYSTEM_USER_ID,
            'chatroom_id' => $this->echo['room_id'] ?? 0,
            'receiver_id' => $this->echo['target_id'] ?? null,
            'bot_id'      => $this->echo['bot_id'] ?? null,
            'message'     => match($command) {
                'banker'        => $this->getBanker(),
                'bans'          => $this->getBans(),
                'doubleupload'  => $this->getDoubleUpload(),
                'freeleech'     => $this->getFreeleech(),
                'king'          => $this->getKing(),
                'logins'        => $this->getLogins(),
                'peers'         => $this->getPeers(),
                'registrations' => $this->getRegistrations(),
                'uploads'       => $this->getUploads(),
                'warnings'      => $this->getWarnings(),
                'seeded'        => $this->getSeeded(),
                'leeched'       => $this->getLeeched(),
                'snatched'      => $this->getSnatched(),
                default         => $this->getHelp(),
            }
        ]);

        CreateMessage::dispatch($message, $echo);
    }
}

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

use App\Events\Chatter;
use App\Http\Resources\UserAudibleResource;
use App\Http\Resources\UserEchoResource;
use App\Models\Ban;
use App\Models\Bot;
use App\Models\Peer;
use App\Models\Torrent;
use App\Models\User;
use App\Models\UserAudible;
use App\Models\UserEcho;
use App\Models\Warning;
use App\Repositories\ChatRepository;
use Illuminate\Support\Carbon;

class NerdBot
{
    private Bot $bot;

    private User $target;

    private string $type;

    private string $message;

    private string $log;

    private Carbon $expiresAt;

    private Carbon $current;

    private string $site;

    public function __construct(private readonly ChatRepository $chatRepository)
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
            $bots = Bot::where('active', '=', 1)->where('id', '!=', $this->bot->id)->orderBy('position')->get();

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

        return "There are currently {$freeleech} freeleech torrents on {$this->site}!";
    }

    public function getDoubleUpload(): string
    {
        $doubleUpload = cache()->remember(
            'nerdbot-doubleupload',
            $this->expiresAt,
            fn () => Torrent::where('doubleup', '=', 1)->count()
        );

        return "There are currently {$doubleUpload} double upload torrents on {$this->site}!";
    }

    public function getPeers(): string
    {
        $peers = cache()->remember(
            'nerdbot-peers',
            $this->expiresAt,
            fn () => Peer::where('active', '=', 1)->count()
        );

        return "Currently there are {$peers} peers on {$this->site}!";
    }

    public function getBans(): string
    {
        $bans = cache()->remember(
            'nerdbot-bans',
            $this->expiresAt,
            fn () => Ban::whereNotNull('ban_reason')
                ->where('created_at', '>', $this->current->subDay())->count()
        );

        return "In the last 24 hours, {$bans} users have been banned from {$this->site}";
    }

    public function getUnbans(): string
    {
        $unbans = cache()->remember(
            'nerdbot-unbans',
            $this->expiresAt,
            fn () => Ban::whereNotNull('unban_reason')
                ->where('removed_at', '>', $this->current->subDay())->count()
        );

        return "In the last 24 hours, {$unbans} users have been unbanned from {$this->site}";
    }

    public function getWarnings(): string
    {
        $warnings = cache()->remember(
            'nerdbot-warnings',
            $this->expiresAt,
            fn () => Warning::where('created_at', '>', $this->current->subDay())->count()
        );

        return "In the last 24 hours, {$warnings} hit and run warnings have been issued on {$this->site}!";
    }

    public function getUploads(): string
    {
        $uploads = cache()->remember(
            'nerdbot-uploads',
            $this->expiresAt,
            fn () => Torrent::where('created_at', '>', $this->current->subDay())->count()
        );

        return "In the last 24 hours, {$uploads} torrents have been uploaded to {$this->site}!";
    }

    public function getLogins(): string
    {
        $logins = cache()->remember(
            'nerdbot-logins',
            $this->expiresAt,
            fn () => User::whereNotNull('last_login')->where('last_login', '>', $this->current->subDay())->count()
        );

        return "In The Last 24 Hours, {$logins} Unique Users Have Logged Into {$this->site}!";
    }

    public function getRegistrations(): string
    {
        $registrations = cache()->remember(
            'nerdbot-users',
            $this->expiresAt,
            fn () => User::where('created_at', '>', $this->current->subDay())->count()
        );

        return "In the last 24 hours, {$registrations} users have registered to {$this->site}!";
    }

    public function getHelp(): string
    {
        return $this->replaceVars($this->bot->help ?? '');
    }

    public function getKing(): string
    {
        return config('other.title').' Is King!';
    }

    /**
     * Process Message.
     */
    public function process(string $type, User $user, string $message): true|\Illuminate\Http\Response
    {
        $this->target = $user;

        if ($type === 'message') {
            [$command,] = mb_split(' +', trim($message), 2) + [null, null];
        } else {
            [, $command,] = mb_split(' +', trim($message), 3) + [null, null, null];
        }

        $this->log = match($command) {
            'banker'        => $this->getBanker(),
            'bans'          => $this->getBans(),
            'unbans'        => $this->getUnbans(),
            'doubleupload'  => $this->getDoubleUpload(),
            'freeleech'     => $this->getFreeleech(),
            'help'          => $this->getHelp(),
            'king'          => $this->getKing(),
            'logins'        => $this->getLogins(),
            'peers'         => $this->getPeers(),
            'registrations' => $this->getRegistrations(),
            'uploads'       => $this->getUploads(),
            'warnings'      => $this->getWarnings(),
            'seeded'        => $this->getSeeded(),
            'leeched'       => $this->getLeeched(),
            'snatched'      => $this->getSnatched(),
            default         => 'All '.$this->bot->name.' commands must be a private message or begin with /'.$this->bot->command.' or !'.$this->bot->command.'. Need help? Type /'.$this->bot->command.' help and you shall be helped.',
        };

        $this->type = $type;
        $this->message = $message;

        return $this->pm();
    }

    /**
     * Output Message.
     */
    public function pm(): true|\Illuminate\Http\Response
    {
        $type = $this->type;
        $target = $this->target;
        $txt = $this->log;
        $message = $this->message;

        if ($type === 'message' || $type === 'private') {
            // Create echo for user if missing
            $echoes = cache()->remember(
                'user-echoes'.$target->id,
                3600,
                fn () => UserEcho::with(['user', 'room', 'target', 'bot'])->where('user_id', '=', $target->id)->get()
            );

            if ($echoes->doesntContain(fn ($echo) => $echo->bot_id == $this->bot->id)) {
                $echoes->push(UserEcho::create([
                    'user_id'   => $target->id,
                    'target_id' => $this->bot->id,
                ]));

                cache()->put('user-echoes'.$target->id, $echoes, 3600);

                Chatter::dispatch('echo', $target->id, UserEchoResource::collection($echoes));
            }

            // Create audible for user if missing
            $audibles = cache()->remember(
                'user-audibles'.$target->id,
                3600,
                fn () => UserAudible::with(['user', 'room', 'target', 'bot'])->where('user_id', '=', $target->id)->get()
            );

            if ($audibles->doesntContain(fn ($audible) => $audible->bot_id == $this->bot->id)) {
                $audibles->push(UserAudible::create([
                    'user_id'   => $target->id,
                    'target_id' => $this->bot->id,
                    'status'    => 0,
                ]));

                cache()->put('user-audibles'.$target->id, $audibles, 3600);

                Chatter::dispatch('audible', $target->id, UserAudibleResource::collection($audibles));
            }

            // Create message
            $roomId = 0;
            $this->chatRepository->privateMessage($target->id, $roomId, $message, 1, $this->bot->id);
            $this->chatRepository->privateMessage(1, $roomId, $txt, $target->id, $this->bot->id);

            return response('success');
        }

        if ($type === 'echo') {
            $roomId = 0;
            $this->chatRepository->botMessage($this->bot->id, $roomId, $txt, $target->id);

            return response('success');
        }

        if ($type === 'public') {
            $this->chatRepository->message($target->id, $target->chatroom->id, $message, null, null);
            $this->chatRepository->message(1, $target->chatroom->id, $txt, null, $this->bot->id);

            return response('success');
        }

        return true;
    }
}

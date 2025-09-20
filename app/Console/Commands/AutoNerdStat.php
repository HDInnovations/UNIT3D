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

namespace App\Console\Commands;

use App\Repositories\ChatRepository;
use Illuminate\Console\Command;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class AutoNerdStat extends Command
{
    /**
     * AutoNerdStat Constructor.
     */
    public function __construct(private readonly ChatRepository $chatRepository)
    {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:nerdstat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically posts daily nerd stat to shoutbox';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        // Check if the nerd bot is enabled in the configuration.
        if (!config('chat.nerd_bot')) {
            return;
        }

        // Define the possible stats.
        $stats = collect([
            'birthday',
            'logins',
            'uploads',
            'users',
            'fl25',
            'fl50',
            'fl75',
            'fl100',
            'du',
            'peers',
            'bans',
            'unbans',
            'warnings',
            'king',
        ])->random();

        // Generate the message based on the selected stat.
        $message = match ($stats) {
            'birthday' => config('other.title').' Birthday Is [b]'.config('other.birthdate').'[/b]!',
            'logins'   => 'In the last 24 hours [color=#93c47d][b]'.DB::table('users')->whereNotNull('last_login')->where('last_login', '>', now()->subDay())->count().'[/b][/color] unique users have logged into '.config('other.title').'!',
            'uploads'  => 'In the last 24 hours [color=#93c47d][b]'.DB::table('torrents')->where('created_at', '>', now()->subDay())->count().'[/b][/color] torrents have been uploaded to '.config('other.title').'!',
            'users'    => 'In the last 24 hours [color=#93c47d][b]'.DB::table('users')->where('created_at', '>', now()->subDay())->count().'[/b][/color] users have registered to '.config('other.title').'!',
            'fl25'     => 'There are currently [color=#93c47d][b]'.DB::table('torrents')->where('free', '=', 25)->count().'[/b][/color] 25% freeleech torrents on '.config('other.title').'!',
            'fl50'     => 'There are currently [color=#93c47d][b]'.DB::table('torrents')->where('free', '=', 50)->count().'[/b][/color] 50% freeleech torrents on '.config('other.title').'!',
            'fl75'     => 'There are currently [color=#93c47d][b]'.DB::table('torrents')->where('free', '=', 75)->count().'[/b][/color] 75% freeleech torrents on '.config('other.title').'!',
            'fl100'    => 'There are currently [color=#93c47d][b]'.DB::table('torrents')->where('free', '=', 100)->count().'[/b][/color] 100% freeleech torrents on '.config('other.title').'!',
            'du'       => 'There are currently [color=#93c47d][b]'.DB::table('torrents')->where('doubleup', '=', 1)->count().'[/b][/color] double upload torrents on '.config('other.title').'!',
            'peers'    => 'Currently there are [color=#93c47d][b]'.DB::table('peers')->where('active', '=', 1)->count().'[/b][/color] peers on '.config('other.title').'!',
            'bans'     => 'In the last 24 hours [color=#dd7e6b][b]'.DB::table('bans')->whereNotNull('ban_reason')->where('created_at', '>', now()->subDay())->count().'[/b][/color] users have been banned from '.config('other.title').'!',
            'unbans'   => 'In the last 24 hours [color=#dd7e6b][b]'.DB::table('bans')->whereNotNull('unban_reason')->where('removed_at', '>', now()->subDay())->count().'[/b][/color] users have been unbanned from '.config('other.title').'!',
            'warnings' => 'In the last 24 hours [color=#dd7e6b][b]'.DB::table('warnings')->where('created_at', '>', now()->subDay())->count().'[/b][/color] hit and run warnings have been issued on '.config('other.title').'!',
            'king'     => config('other.title').' is king!',
            default    => 'Nerd stat error!',
        };

        // Post the message to the chatbox.
        $this->chatRepository->systemMessage($message);

        // Output a success message to the console.
        $this->comment('Automated nerd stat command complete');
    }
}

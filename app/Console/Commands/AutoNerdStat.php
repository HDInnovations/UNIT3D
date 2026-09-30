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
        $site = config('other.title');
        $locale = config('app.locale');

        $message = match ($stats) {
            'birthday' => trans('application-messages.bot.nerdstat-birthday', ['site' => $site, 'date' => config('other.birthdate')], $locale),
            'logins'   => trans('application-messages.bot.nerdstat-logins', ['count' => DB::table('users')->whereNotNull('last_login')->where('last_login', '>', now()->subDay())->count(), 'site' => $site], $locale),
            'uploads'  => trans('application-messages.bot.nerdstat-uploads', ['count' => DB::table('torrents')->where('created_at', '>', now()->subDay())->count(), 'site' => $site], $locale),
            'users'    => trans('application-messages.bot.nerdstat-users', ['count' => DB::table('users')->where('created_at', '>', now()->subDay())->count(), 'site' => $site], $locale),
            'fl25'     => trans('application-messages.bot.nerdstat-fl', ['count' => DB::table('torrents')->where('free', '=', 25)->count(), 'percent' => 25, 'site' => $site], $locale),
            'fl50'     => trans('application-messages.bot.nerdstat-fl', ['count' => DB::table('torrents')->where('free', '=', 50)->count(), 'percent' => 50, 'site' => $site], $locale),
            'fl75'     => trans('application-messages.bot.nerdstat-fl', ['count' => DB::table('torrents')->where('free', '=', 75)->count(), 'percent' => 75, 'site' => $site], $locale),
            'fl100'    => trans('application-messages.bot.nerdstat-fl', ['count' => DB::table('torrents')->where('free', '=', 100)->count(), 'percent' => 100, 'site' => $site], $locale),
            'du'       => trans('application-messages.bot.nerdstat-du', ['count' => DB::table('torrents')->where('doubleup', '=', 1)->count(), 'site' => $site], $locale),
            'peers'    => trans('application-messages.bot.nerdstat-peers', ['count' => DB::table('peers')->where('active', '=', 1)->count(), 'site' => $site], $locale),
            'bans'     => trans('application-messages.bot.nerdstat-bans', ['count' => DB::table('bans')->whereNotNull('ban_reason')->where('created_at', '>', now()->subDay())->count(), 'site' => $site], $locale),
            'unbans'   => trans('application-messages.bot.nerdstat-unbans', ['count' => DB::table('bans')->whereNotNull('unban_reason')->where('removed_at', '>', now()->subDay())->count(), 'site' => $site], $locale),
            'warnings' => trans('application-messages.bot.nerdstat-warnings', ['count' => DB::table('warnings')->where('created_at', '>', now()->subDay())->count(), 'site' => $site], $locale),
            'king'     => trans('application-messages.bot.site-is-king-lower', ['site' => $site], $locale),
            default    => trans('application-messages.bot.nerdstat-error', [], $locale),
        };

        // Post the message to the chatbox.
        $this->chatRepository->systemMessage($message);

        // Output a success message to the console.
        $this->comment('Automated nerd stat command complete');
    }
}

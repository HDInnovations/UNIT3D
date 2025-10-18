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

namespace App\Http\Controllers;

use App\Models\Group;
use App\Helpers\Language;
use App\Models\Peer;
use App\Models\Torrent;
use App\Models\TorrentRequest;
use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Concurrency;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\StatsControllerTest
 */
class StatsController extends Controller
{
    public Carbon $carbon;

    /**
     * StatsController Constructor.
     */
    public function __construct()
    {
        $this->carbon = Carbon::now()->addMinutes(10);
    }

    /**
     * Show Extra-Stats Index.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('stats.index');
    }

    /**
     * Show Extra-Stats Users.
     *
     * @throws Exception
     */
    public function uploaded(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('stats.users.uploaded', [
            'uploaded' => User::query()
                ->with('group')
                ->whereDoesntHave('group', fn ($query) => $query->whereIn('slug', ['banned', 'validating', 'disabled', 'pruned']))
                ->orderByDesc('uploaded')
                ->take(100)
                ->get(),
        ]);
    }

    /**
     * Show Extra-Stats Users.
     *
     * @throws Exception
     */
    public function downloaded(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('stats.users.downloaded', [
            'downloaded' => User::query()
                ->with('group')
                ->whereDoesntHave('group', fn ($query) => $query->whereIn('slug', ['banned', 'validating', 'disabled', 'pruned']))
                ->orderByDesc('downloaded')
                ->take(100)
                ->get(),
        ]);
    }

    /**
     * Show Extra-Stats Users.
     */
    public function seeders(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('stats.users.seeders', [
            'seeders' => Peer::with('user.group')
                ->select(DB::raw('user_id, count(distinct torrent_id) as value'))
                ->where('seeder', '=', 1)
                ->where('active', '=', 1)
                ->groupBy('user_id')
                ->orderByDesc('value')
                ->take(100)
                ->get(),
        ]);
    }

    /**
     * Show Extra-Stats Users.
     */
    public function leechers(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('stats.users.leechers', [
            'leechers' => Peer::query()
                ->with('user.group')
                ->select(DB::raw('user_id, count(*) as value'))
                ->where('seeder', '=', 0)
                ->where('active', '=', 1)
                ->groupBy('user_id')
                ->orderByDesc('value')
                ->take(100)
                ->get(),
        ]);
    }

    /**
     * Show Extra-Stats Users.
     */
    public function uploaders(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('stats.users.uploaders', [
            'uploaders' => Torrent::query()
                ->with('user.group')
                ->where('anon', '=', false)
                ->select(DB::raw('user_id, count(*) as value'))
                ->groupBy('user_id')
                ->orderByDesc('value')
                ->take(100)
                ->get(),
        ]);
    }

    /**
     * Show Extra-Stats Users.
     *
     * @throws Exception
     */
    public function bankers(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('stats.users.bankers', [
            'bankers' => User::query()
                ->with('group')
                ->orderByDesc('seedbonus')
                ->whereDoesntHave('group', fn ($query) => $query->whereIn('slug', ['banned', 'validating', 'disabled', 'pruned']))
                ->take(100)
                ->get(),
        ]);
    }

    /**
     * Show Extra-Stats Users.
     */
    public function seedtime(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('stats.users.seedtime', [
            'users' => User::query()
                ->with('group')
                ->withSum('history as seedtime', 'seedtime')
                ->orderByDesc('seedtime')
                ->take(100)
                ->get(),
        ]);
    }

    /**
     * Show Extra-Stats Users.
     */
    public function seedsize(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('stats.users.seedsize', [
            'users' => User::query()
                ->with('group')
                ->withSum('seedingTorrents as seedsize', 'size')
                ->orderByDesc('seedsize')
                ->take(100)
                ->get(),
        ]);
    }

    /**
     * Show Extra-Stats Users.
     */
    public function uploadSnatches(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('stats.users.upload-snatches', [
            'users' => User::query()
                ->with('group')
                ->withCount('uploadSnatches')
                ->orderByDesc('upload_snatches_count')
                ->take(100)
                ->get(),
        ]);
    }

    /**
     * Show Extra-Stats Torrents.
     */
    public function seeded(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('stats.torrents.seeded', [
            'seeded' => Torrent::orderByDesc('seeders')->take(100)->get(),
        ]);
    }

    /**
     * Show Extra-Stats Torrents.
     */
    public function leeched(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('stats.torrents.leeched', [
            'leeched' => Torrent::orderByDesc('leechers')->take(100)->get(),
        ]);
    }

    /**
     * Show Extra-Stats Torrents.
     */
    public function completed(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('stats.torrents.completed', [
            'completed' => Torrent::orderByDesc('times_completed')->take(100)->get(),
        ]);
    }

    /**
     * Show Extra-Stats Torrents.
     */
    public function dying(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('stats.torrents.dying', [
            'dying' => Torrent::where('seeders', '=', 1)
                ->where('times_completed', '>=', 1)
                ->orderByDesc('leechers')
                ->take(100)
                ->get(),
        ]);
    }

    /**
     * Show Extra-Stats Torrents.
     */
    public function dead(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('stats.torrents.dead', [
            'dead' => Torrent::where('seeders', '=', 0)
                ->orderByDesc('leechers')
                ->take(100)
                ->get(),
        ]);
    }

    /**
     * Show Extra-Stats Torrent Requests.
     */
    public function bountied(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('stats.requests.bountied', [
            'bountied' => TorrentRequest::orderByDesc('bounty')->take(100)->get(),
        ]);
    }

    /**
     * Show Extra-Stats Groups.
     */
    public function groups(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('stats.groups.groups', [
            'groups' => Group::orderBy('position')->withCount(['users' => fn ($query) => $query->withTrashed()])->get(),
        ]);
    }

    /**
     * Show Group Requirements.
     */
    public function groupsRequirements(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $user = auth()->user();

        return view('stats.groups.groups-requirements', [
            'current'           => Carbon::now(),
            'user'              => $user,
            'user_avg_seedtime' => DB::table('history')->where('user_id', '=', $user->id)->avg('seedtime'),
            'user_account_age'  => (int) Carbon::now()->diffInSeconds($user->created_at, true),
            'user_seed_size'    => $user->seedingTorrents()->sum('size'),
            'user_uploads'      => $user->torrents()->count(),
            'groups'            => Group::orderBy('position')->where('is_modo', '=', 0)->get(),
        ]);
    }

    /**
     * Show Extra-Stats Languages.
     */
    public function languages(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('stats.languages.languages', [
            'languages' => Language::allowed(),
        ]);
    }

    /**
     * Show Extra-Stats Clients.
     */
    public function clients(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        [
            $groupedClients,
            $clients,
            $singleSeedClientGroups,
            $singleSeedClients,
        ] = Concurrency::run([
            fn () => cache()->flexible(
                'stats:client-groups',
                [3600, 2 * 24 * 3600],
                fn () => Peer::query()
                    ->select([
                        DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(agent, '/', 1), ' ', 1) AS prefix"),
                        DB::raw('COUNT(DISTINCT user_id) AS user_count'),
                        DB::raw('COUNT(DISTINCT torrent_id) AS torrent_count'),
                        DB::raw('COUNT(*) AS peer_count'),
                    ])
                    ->where('active', '=', true)
                    ->where('visible', '=', true)
                    ->groupByRaw('prefix')
                    ->orderBy('prefix')
                    ->get()
                    ->keyBy('prefix')
                    ->toArray(),
            ),
            fn () => cache()->flexible(
                'stats:clients',
                [3600, 2 * 24 * 3600],
                fn () => Peer::query()
                    ->select([
                        'agent',
                        DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(agent, '/', 1), ' ', 1) AS prefix"),
                        DB::raw('COUNT(DISTINCT user_id) AS user_count'),
                        DB::raw('COUNT(DISTINCT torrent_id) AS torrent_count'),
                        DB::raw('COUNT(*) AS peer_count'),
                    ])
                    ->where('active', '=', true)
                    ->where('visible', '=', true)
                    ->groupBy('agent')
                    ->orderBy('agent')
                    ->get()
                    ->keyBy('agent')
                    ->toArray(),
            ),
            fn () => cache()->flexible(
                'stats:client-groups-single-seeded',
                [3600, 2 * 24 * 3600],
                fn () => Peer::query()
                    ->select([
                        'agent',
                        DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(agent, '/', 1), ' ', 1) AS prefix"),
                        DB::raw('COUNT(DISTINCT torrent_id) AS single_seed_count'),
                    ])
                    ->where('active', '=', true)
                    ->where('visible', '=', true)
                    ->whereIn(
                        'torrent_id',
                        Peer::query()
                            ->select('torrent_id')
                            ->where('active', '=', true)
                            ->where('visible', '=', true)
                            ->groupBy('torrent_id')
                            ->havingRaw('COUNT(*) = 1')
                    )
                    ->groupBy('prefix')
                    ->orderBy('prefix')
                    ->get()
                    ->keyBy('prefix')
                    ->toArray(),
            ),
            fn () => cache()->flexible(
                'stats:clients-single-seeded',
                [3600, 2 * 24 * 3600],
                fn () => Peer::query()
                    ->select([
                        'agent',
                        DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(agent, '/', 1), ' ', 1) AS prefix"),
                        DB::raw('COUNT(DISTINCT torrent_id) AS single_seed_count'),
                    ])
                    ->where('active', '=', true)
                    ->where('visible', '=', true)
                    ->whereIn(
                        'torrent_id',
                        Peer::query()
                            ->select('torrent_id')
                            ->where('active', '=', true)
                            ->where('visible', '=', true)
                            ->groupBy('torrent_id')
                            ->havingRaw('COUNT(*) = 1')
                    )
                    ->groupBy(['agent'])
                    ->orderBy('agent')
                    ->get()
                    ->keyBy('agent')
                    ->toArray(),
            )
        ]);

        foreach ($clients as $agent => $client) {
            $groupedClients[$client['prefix']]['clients'][$agent] = $client;
        }

        foreach ($singleSeedClientGroups as $prefix => $client) {
            $groupedClients[$prefix]['single_seed_count'] = $client['single_seed_count'];
        }

        foreach ($singleSeedClients as $agent => $client) {
            $groupedClients[$client['prefix']]['clients'][$agent]['single_seed_count'] = $client['single_seed_count'];
        }

        return view('stats.clients.clients', [
            'clients' => $groupedClients,
        ]);
    }

    /**
     * Show Extra-Stats Themes.
     */
    public function themes(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('stats.themes.index', [
            'siteThemes' => User::query()
                ->withTrashed()
                ->leftJoin('user_settings', 'users.id', '=', 'user_settings.user_id')
                ->selectRaw('COALESCE(style, ?) as total_style, COUNT(*) as value', [config('other.default_style')])
                ->groupBy('total_style')
                ->orderByDesc('value')
                ->get(),
            'customThemes' => UserSetting::where('custom_css', '!=', '')
                ->select(DB::raw('custom_css, count(*) as value'))
                ->groupBy('custom_css')
                ->orderByDesc('value')
                ->get(),
            'standaloneThemes' => UserSetting::whereNotNull('standalone_css')
                ->select(DB::raw('standalone_css, count(*) as value'))
                ->groupBy('standalone_css')
                ->orderByDesc('value')
                ->get(),
        ]);
    }

    /**
     * Show Extra-Stats User Messages.
     */
    public function messages(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $users = User::query()
            ->with('group')
            ->withCount([
                'messages' => fn ($query) => $query->where('chatroom_id', '!=', 0), // Exclude private chatbox messages;
            ])
            ->withSum(
                ['messages as characters_typed' => fn ($query) => $query->where('chatroom_id', '!=', 0)],  // Exclude private chatbox messages
                DB::raw('CHAR_LENGTH(message)')
            )
            ->orderByDesc('messages_count')
            ->where('id', '!=', User::SYSTEM_USER_ID)
            ->whereDoesntHave('group', fn ($query) => $query->whereIn('slug', ['banned', 'validating', 'disabled', 'pruned', 'bot']))
            ->take(100)
            ->get();

        return view('stats.users.messages', [
            'users' => $users,
        ]);
    }
}

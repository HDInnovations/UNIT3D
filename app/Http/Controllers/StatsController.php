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
use App\Models\Language;
use App\Models\Peer;
use App\Models\Torrent;
use App\Models\TorrentRequest;
use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

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
            'uploaded' => User::orderByDesc('uploaded')
                ->whereDoesntHave('group', fn ($query) => $query->whereIn('slug', ['banned', 'validating', 'disabled', 'pruned']))
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
            'downloaded' => User::orderByDesc('downloaded')
                ->whereDoesntHave('group', fn ($query) => $query->whereIn('slug', ['banned', 'validating', 'disabled', 'pruned']))
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
            'seeders' => Peer::with('user')
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
            'leechers' => Peer::with('user')
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
            'uploaders' => Torrent::with('user')
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
            'bankers' => User::orderByDesc('seedbonus')
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
            'users' => User::withSum('history as seedtime', 'seedtime')
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
            'users' => User::withSum('seedingTorrents as seedsize', 'size')
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
            'users' => User::withCount('uploadSnatches')
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
            'user_account_age'  => Carbon::now()->diffInSeconds($user->created_at),
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
        $clients = cache()->remember(
            'stats:clients',
            3600,
            fn () => Peer::selectRaw('agent, COUNT(*) as user_count, SUM(peer_count) as peer_count')
                ->fromSub(
                    Peer::query()
                        ->select(['agent', 'user_id', DB::raw('COUNT(*) as peer_count')])
                        ->groupBy('agent', 'user_id')
                        ->where('active', '=', true),
                    'distinct_agent_user'
                )
                ->groupBy('agent')
                ->orderBy('agent')
                ->get()
                ->toArray()
        );

        $groupedClients = [];

        foreach ($clients as $client) {
            $prefix = preg_split('/\/| /', $client['agent'], 2)[0] ?? $client['agent'];

            if (\array_key_exists($prefix, $groupedClients)) {
                $groupedClients[$prefix]['user_count'] += $client['user_count'];
                $groupedClients[$prefix]['peer_count'] += $client['peer_count'];
            } else {
                $groupedClients[$prefix]['user_count'] = $client['user_count'];
                $groupedClients[$prefix]['peer_count'] = $client['peer_count'];
            }

            $groupedClients[$prefix]['clients'][] = $client;
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
            'siteThemes' => UserSetting::select(DB::raw('style, count(*) as value'))
                ->groupBy('style')
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
}

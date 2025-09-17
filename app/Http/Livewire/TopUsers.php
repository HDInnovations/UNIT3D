<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Comment;
use App\Models\History;
use App\Models\Peer;
use App\Models\Post;
use App\Models\Thank;
use App\Models\Torrent;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TopUsers extends Component
{
    public string $tab = 'uploaders';

    /**
     * @var \Illuminate\Support\Collection<int, Torrent>
     */
    final protected \Illuminate\Support\Collection $uploaders {
        get => cache()->flexible(
            'top-users:uploaders',
            [3600, 3600 * 2],
            fn () => Torrent::query()
                ->with(['user.group'])
                ->select(DB::raw('user_id, COUNT(user_id) as value'))
                ->where('user_id', '!=', User::SYSTEM_USER_ID)
                ->where('anon', '=', false)
                ->groupBy('user_id')
                ->orderByDesc('value')
                ->take(8)
                ->get(),
        );
    }

    /**
     * @var \Illuminate\Support\Collection<int, History>
     */
    final protected \Illuminate\Support\Collection $downloaders {
        get => cache()->flexible(
            'top-users:downloaders',
            [3600, 3600 * 2],
            fn () => History::query()
                ->with(['user.group'])
                ->select(DB::raw('user_id, count(distinct torrent_id) as value'))
                ->whereNotNull('completed_at')
                ->where('user_id', '!=', User::SYSTEM_USER_ID)
                ->groupBy('user_id')
                ->orderByDesc('value')
                ->take(8)
                ->get(),
        );
    }

    /**
     * @var \Illuminate\Support\Collection<int, User>
     */
    final protected \Illuminate\Support\Collection $uploaded {
        get => cache()->flexible(
            'top-users:uploaded',
            [3600, 3600 * 2],
            fn () => User::query()
                ->select(['id', 'group_id', 'username', 'uploaded', 'image'])
                ->with('group')
                ->where('id', '!=', User::SYSTEM_USER_ID)
                ->whereDoesntHave('group', fn ($query) => $query->whereIn('slug', ['banned', 'validating', 'disabled', 'pruned']))
                ->orderByDesc('uploaded')
                ->take(8)
                ->get(),
        );
    }

    /**
     * @var \Illuminate\Support\Collection<int, User>
     */
    final protected \Illuminate\Support\Collection $downloaded {
        get => cache()->flexible(
            'top-users:downloaded',
            [3600, 3600 * 2],
            fn () => User::query()
                ->select(['id', 'group_id', 'username', 'downloaded', 'image'])
                ->with('group')
                ->where('id', '!=', User::SYSTEM_USER_ID)
                ->whereDoesntHave('group', fn ($query) => $query->whereIn('slug', ['banned', 'validating', 'disabled', 'pruned']))
                ->orderByDesc('downloaded')
                ->take(8)
                ->get(),
        );
    }

    /**
     * @var \Illuminate\Support\Collection<int, Peer>
     */
    final protected \Illuminate\Support\Collection $seeders {
        get => cache()->flexible(
            'top-users:seeders',
            [3600, 3600 * 2],
            fn () => Peer::query()
                ->with(['user.group'])
                ->select(DB::raw('user_id, count(distinct torrent_id) as value'))
                ->where('user_id', '!=', User::SYSTEM_USER_ID)
                ->where('seeder', '=', 1)
                ->where('active', '=', 1)
                ->groupBy('user_id')
                ->orderByDesc('value')
                ->take(8)
                ->get(),
        );
    }

    /**
     * @var \Illuminate\Support\Collection<int, User>
     */
    final protected \Illuminate\Support\Collection $seedtimes {
        get => cache()->flexible(
            'top-users:seedtimes',
            [3600, 3600 * 2],
            fn () => User::query()
                ->withSum('history as seedtime', 'seedtime')
                ->with('group')
                ->where('id', '!=', User::SYSTEM_USER_ID)
                ->whereDoesntHave('group', fn ($query) => $query->whereIn('slug', ['banned', 'validating', 'disabled', 'pruned']))
                ->orderByDesc('seedtime')
                ->take(8)
                ->get(),
        );
    }

    /**
     * @var \Illuminate\Support\Collection<int, User>
     */
    final protected \Illuminate\Support\Collection $served {
        get => cache()->flexible(
            'top-users:served',
            [3600, 3600 * 2],
            fn () => User::query()
                ->withCount('uploadSnatches')
                ->with('group')
                ->where('id', '!=', User::SYSTEM_USER_ID)
                ->whereDoesntHave('group', fn ($query) => $query->whereIn('slug', ['banned', 'validating', 'disabled', 'pruned']))
                ->orderByDesc('upload_snatches_count')
                ->take(8)
                ->get(),
        );
    }

    /**
     * @var \Illuminate\Support\Collection<int, Comment>
     */
    final protected \Illuminate\Support\Collection $commenters {
        get => cache()->flexible(
            'top-users:commenters',
            [3600, 3600 * 2],
            fn () => Comment::query()
                ->with(['user.group'])
                ->select(DB::raw('user_id, COUNT(user_id) as value'))
                ->where('user_id', '!=', User::SYSTEM_USER_ID)
                ->where('anon', '=', false)
                ->groupBy('user_id')
                ->orderByDesc('value')
                ->take(8)
                ->get(),
        );
    }

    /**
     * @var \Illuminate\Support\Collection<int, Post>
     */
    final protected \Illuminate\Support\Collection $posters {
        get => cache()->flexible(
            'top-users:posters',
            [3600, 3600 * 2],
            fn () => Post::query()
                ->with(['user.group'])
                ->select(DB::raw('user_id, COUNT(user_id) as value'))
                ->where('user_id', '!=', User::SYSTEM_USER_ID)
                ->groupBy('user_id')
                ->orderByDesc('value')
                ->take(8)
                ->get(),
        );
    }

    /**
     * @var \Illuminate\Support\Collection<int, Thank>
     */
    final protected \Illuminate\Support\Collection $thankers {
        get => cache()->flexible(
            'top-users:thankers',
            [3600, 3600 * 2],
            fn () => Thank::query()
                ->with(['user.group'])
                ->select(DB::raw('user_id, COUNT(user_id) as value'))
                ->where('user_id', '!=', User::SYSTEM_USER_ID)
                ->groupBy('user_id')
                ->orderByDesc('value')
                ->take(8)
                ->get(),
        );
    }

    /**
     * @var \Illuminate\Support\Collection<int, Torrent>
     */
    final protected \Illuminate\Support\Collection $personals {
        get => cache()->flexible(
            'top-users:personals',
            [3600, 3600 * 2],
            fn () => Torrent::query()
                ->with(['user.group'])
                ->select(DB::raw('user_id, COUNT(user_id) as value'))
                ->where('user_id', '!=', User::SYSTEM_USER_ID)
                ->where('anon', '=', false)
                ->where('personal_release', '=', true)
                ->groupBy('user_id')
                ->orderByDesc('value')
                ->take(8)
                ->get(),
        );
    }

    public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.top-users');
    }
}

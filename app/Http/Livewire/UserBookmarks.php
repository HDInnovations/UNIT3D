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
 * @author     Roardom <roardom@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Livewire;

use App\Models\Bookmark;
use App\Models\User;
use App\Traits\LivewireSort;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class UserBookmarks extends Component
{
    use LivewireSort;
    use WithPagination;

    public ?User $user = null;

    #TODO: Update URL attributes once Livewire 3 fixes upstream bug. See: https://github.com/livewire/livewire/discussions/7746

    #[Url(history: true)]
    public string $sortField = 'bookmarks.created_at';

    #[Url(history: true)]
    public string $sortDirection = 'desc';

    final public function mount(int $userId): void
    {
        $this->user = User::find($userId);
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, Bookmark>
     */
    #[Computed]
    final public function bookmarks(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Bookmark::query()
            ->select([
                'bookmarks.torrent_id',
                'bookmarks.created_at as bookmark_created_at',
                'torrents.name',
                'torrents.seeders',
                'torrents.leechers',
                'torrents.times_completed',
                'torrents.size',
                'torrents.created_at as torrent_created_at',
            ])
            ->withCasts([
                'bookmark_created_at' => 'datetime',
                'torrent_created_at'  => 'datetime',
            ])
            ->join('torrents', 'torrents.id', '=', 'bookmarks.torrent_id')
            ->where('bookmarks.user_id', '=', $this->user->id)
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(25);
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.user-bookmarks', [
            'bookmarks' => $this->bookmarks,
        ]);
    }
}

<?php

declare(strict_types=1);

/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.tx
 *
 * @project    UNIT3D Community Edition
 *
 * @author     Roardom <roardom@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Livewire;

use App\Models\Playlist;
use App\Models\PlaylistCategory;
use App\Traits\LivewireSort;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class PlaylistSearch extends Component
{
    use LivewireSort;
    use WithPagination;

    #TODO: Update URL attributes once Livewire 3 fixes upstream bug. See: https://github.com/livewire/livewire/discussions/7746

    #[Url(history: true)]
    public string $name = '';

    #[Url(history: true)]
    public int $perPage = 24;

    #[Url(history: true)]
    public string $sortField = 'name';

    #[Url(history: true)]
    public string $username = '';

    #[Url(history: true)]
    public string $playlistCategoryId = "__any";

    #[Url(history: true)]
    public string $sortDirection = 'asc';

    final public function updatingName(): void
    {
        $this->resetPage();
    }

    /**
     * @var \Illuminate\Pagination\LengthAwarePaginator<int, Playlist>
     */
    final protected $playlists {
        get => Playlist::query()
            ->with([
                'user:id,username,group_id,image' => ['group'],
            ])
            ->withCount('torrents')
            ->when(
                ! auth()->user()->group->is_modo,
                fn ($query) => $query
                    ->where(
                        fn ($query) => $query
                            ->where('is_private', '=', 0)
                            ->orWhere(fn ($query) => $query->where('is_private', '=', 1)->where('user_id', '=', auth()->id()))
                    )
            )
            ->when($this->name !== '', fn ($query) => $query->where('name', 'LIKE', '%'.str_replace(' ', '%', $this->name).'%'))
            ->when($this->username !== '', fn ($query) => $query->whereRelation('user', 'username', 'LIKE', '%'.$this->username.'%'))
            ->when($this->playlistCategoryId !== "__any", fn ($query) => $query->where('playlist_category_id', '=', $this->playlistCategoryId))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(min($this->perPage, 100));
    }

    /**
     * @var \Illuminate\Database\Eloquent\Collection<int, PlaylistCategory>
     */
    final protected \Illuminate\Database\Eloquent\Collection $playlistCategories {
        get => cache()->flexible(
            'playlist-categories',
            [3600, 3600 * 2],
            fn () => PlaylistCategory::query()->orderBy('position')->get()
        );
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.playlist-search', [
            'playlists'          => $this->playlists,
            'playlistCategories' => $this->playlistCategories,
        ]);
    }
}

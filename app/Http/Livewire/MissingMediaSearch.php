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

namespace App\Http\Livewire;

use App\Models\TmdbMovie;
use App\Models\Type;
use App\Traits\LivewireSort;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class MissingMediaSearch extends Component
{
    use LivewireSort;
    use WithPagination;

    #TODO: Update URL attributes once Livewire 3 fixes upstream bug. See: https://github.com/livewire/livewire/discussions/7746

    #[Url(history: true)]
    public string $name = '';

    #[Url(history: true)]
    public ?int $year = null;

    #[Url(history: true)]
    public array $categories = [];

    #[Url(history: true)]
    public string $sortField = 'created_at';

    #[Url(history: true)]
    public string $sortDirection = 'desc';

    #[Url(history: true)]
    public int $perPage = 50;

    /**
     * @var \Illuminate\Pagination\LengthAwarePaginator<int, TmdbMovie>
     */
    final protected \Illuminate\Pagination\LengthAwarePaginator $medias {
        get => TmdbMovie::query()
            ->with(['torrents:tmdb_movie_id,tmdb_tv_id,resolution_id,type_id' => ['resolution:id,position,name']])
            ->when($this->name, fn ($query) => $query->where('title', 'LIKE', '%'.$this->name.'%'))
            ->when($this->year, fn ($query) => $query->where('release_date', 'LIKE', '%'.$this->year.'%'))
            ->withCount(['requests' => fn ($query) => $query->whereNull('torrent_id')->whereDoesntHave('claim')])
            ->withMin('torrents', 'category_id')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    /**
     * @var \Illuminate\Database\Eloquent\Collection<int, Type>
     */
    final protected \Illuminate\Database\Eloquent\Collection $types {
        get => Type::query()
            ->select(['id', 'position', 'name'])
            ->orderBy('position')
            ->get();
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.missing-media-search', ['medias' => $this->medias, 'types' => $this->types]);
    }
}

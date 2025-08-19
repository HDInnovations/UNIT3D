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

use App\Models\TmdbPerson;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class TmdbPersonSearch extends Component
{
    use WithPagination;

    #TODO: Update URL attributes once Livewire 3 fixes upstream bug. See: https://github.com/livewire/livewire/discussions/7746

    #[Url(history: true)]
    public string $search = '';

    /**
     * @var string[]
     */
    #[Url(history: true)]
    public array $occupationIds = [];

    #[Url(history: true)]
    public string $firstCharacter = '';

    final public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * @var \Illuminate\Pagination\LengthAwarePaginator<int, TmdbPerson>
     */
    final protected \Illuminate\Pagination\LengthAwarePaginator $persons {
        get => TmdbPerson::query()
            ->select(['id', 'still', 'name'])
            ->when($this->search !== '', fn ($query) => $query->where('name', 'LIKE', '%'.$this->search.'%'))
            ->when($this->occupationIds !== [], fn ($query) => $query->whereHas('credits', fn ($query) => $query->whereIn('occupation_id', $this->occupationIds)))
            ->when($this->firstCharacter !== '', fn ($query) => $query->where('name', 'LIKE', $this->firstCharacter.'%'))
            ->oldest('name')
            ->paginate(36);
    }

    /**
     * @var \Illuminate\Database\Eloquent\Collection<int, TmdbPerson>
     */
    final protected \Illuminate\Database\Eloquent\Collection $firstCharacters {
        get => TmdbPerson::query()
            ->selectRaw('substr(name, 1, 1) as alpha, count(*) as count')
            ->when($this->search !== '', fn ($query) => $query->where('name', 'LIKE', '%'.$this->search.'%'))
            ->when($this->occupationIds !== [], fn ($query) => $query->whereHas('credits', fn ($query) => $query->whereIn('occupation_id', $this->occupationIds)))
            ->groupBy('alpha')
            ->orderBy('alpha')
            ->get();
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.tmdb-person-search', [
            'persons'         => $this->persons,
            'firstCharacters' => $this->firstCharacters,
        ]);
    }
}

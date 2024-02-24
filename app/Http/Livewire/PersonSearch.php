<?php
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

use App\Models\Person;
use Livewire\Component;
use Livewire\WithPagination;

class PersonSearch extends Component
{
    use WithPagination;

    public string $search = '';

    /**
     * @var string[]
     */
    public array $occupationIds = [];

    public string $firstCharacter = '';

    final public function updatedPage(): void
    {
        $this->emit('paginationChanged');
    }

    final public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<Person>
     */
    final public function getPersonsProperty(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Person::select(['id', 'still', 'name'])
            ->whereNotNull('still')
            ->when($this->search !== '', fn ($query) => $query->where('name', 'LIKE', '%'.$this->search.'%'))
            ->when($this->occupationIds !== [], fn ($query) => $query->whereHas('credits', fn ($query) => $query->whereIn('occupation_id', $this->occupationIds)))
            ->when($this->firstCharacter !== '', fn ($query) => $query->where('name', 'LIKE', $this->firstCharacter.'%'))
            ->oldest('name')
            ->paginate(36);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Person>
     */
    final public function getFirstCharactersProperty()
    {
        return Person::selectRaw('substr(name, 1, 1) as alpha, count(*) as count')
            ->when($this->search !== '', fn ($query) => $query->where('name', 'LIKE', '%'.$this->search.'%'))
            ->when($this->occupationIds !== [], fn ($query) => $query->whereHas('credits', fn ($query) => $query->whereIn('occupation_id', $this->occupationIds)))
            ->groupBy('alpha')
            ->orderBy('alpha')
            ->get();
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.person-search', [
            'persons'         => $this->persons,
            'firstCharacters' => $this->firstCharacters,
        ]);
    }
}

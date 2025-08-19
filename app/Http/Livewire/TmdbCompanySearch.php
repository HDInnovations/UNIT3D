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

use App\Models\TmdbCompany;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class TmdbCompanySearch extends Component
{
    use WithPagination;

    #TODO: Update URL attributes once Livewire 3 fixes upstream bug. See: https://github.com/livewire/livewire/discussions/7746

    #[Url(history: true)]
    public string $search = '';

    final public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * @var \Illuminate\Pagination\LengthAwarePaginator<int, TmdbCompany>
     */
    final protected \Illuminate\Pagination\LengthAwarePaginator $companies {
        get => TmdbCompany::query()
            ->withCount([
                'movie' => fn ($query) => $query->has('torrents'),
                'tv'    => fn ($query) => $query->has('torrents'),
            ])
            ->where(fn ($query) => $query->whereHas('movieTorrents')->orWhereHas('tvTorrents'))
            ->when($this->search !== '', fn ($query) => $query->where('name', 'LIKE', '%'.$this->search.'%'))
            ->oldest('name')
            ->paginate(30);
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.tmdb-company-search', [
            'companies' => $this->companies,
        ]);
    }
}

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

use App\Enums\Occupation;
use App\Models\TmdbMovie;
use App\Models\TmdbTv;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class WorkTmdbActorCredits extends Component
{
    public TmdbMovie|TmdbTv|null $work = null;

    public int $page = 1;

    public function loadMore(): void
    {
        $this->page++;
    }

    /**
     * @var Collection<int, \App\Models\TmdbCredit>|null
     */
    final protected ?Collection $credits {
        get => $this->work === null ? null : match ($this->work::class) {
            TmdbMovie::class => $this->work
                ->credits()
                ->with(['person:id,still,name', 'occupation'])
                ->where('occupation_id', '=', Occupation::ACTOR)
                ->orderBy('order')
                ->forPage($this->page, 500)
                ->get(),
            TmdbTv::class => $this->work
                ->credits()
                ->with(['person:id,still,name', 'occupation'])
                ->where('occupation_id', '=', Occupation::ACTOR)
                ->orderBy('order')
                ->forPage($this->page, 500)
                ->get(),
        };
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.work-tmdb-actor-credits', [
            'credits' => $this->credits,
        ]);
    }
}

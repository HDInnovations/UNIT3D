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

use App\Models\Person;
use App\Models\Torrent;
use Livewire\Component;
use Meilisearch\Endpoints\Indexes;

class QuickSearchDropdown extends Component
{
    public string $quicksearchRadio = 'movies';

    public string $quicksearchText = '';

    public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $search = '%'.str_replace(' ', '%', $this->quicksearchText).'%';

        $filters = [
            'deleted_at IS NULL',
            'status = 1',
        ];

        $searchById = false;

        if (preg_match('/^(\d+)$/', $this->quicksearchText, $matches)) {
            $filters[] = 'tmdb = '.$matches[1];
            $searchById = true;
        }

        if (preg_match('/tt0*(?=(\d{7,}))/', $this->quicksearchText, $matches)) {
            $filters[] = 'imdb = '.$matches[1];
            $searchById = true;
        }

        $searchResults = [];

        switch ($this->quicksearchRadio) {
            case 'movies':
                $filters[] = 'category.movie_meta = true';
                $filters[] = 'movie.name EXISTS';

                $searchResults = Torrent::search(
                    $searchById ? '' : $this->quicksearchText,
                    function (Indexes $meilisearch, string $query, array $options) use ($filters) {
                        $options['filter'] = $filters;
                        $options['distinct'] = 'movie.id';

                        return $meilisearch->search($query, $options);
                    }
                )
                    ->simplePaginateRaw(20);

                break;
            case 'series':
                $filters[] = 'category.tv_meta = true';
                $filters[] = 'tv.name EXISTS';

                $searchResults = Torrent::search(
                    $searchById ? '' : $this->quicksearchText,
                    function (Indexes $meilisearch, string $query, array $options) use ($filters) {
                        $options['filter'] = $filters;
                        $options['distinct'] = 'tv.id';

                        return $meilisearch->search($query, $options);
                    }
                )
                    ->simplePaginateRaw(20);

                break;
            case 'persons':
                $searchResults = Person::query()
                    ->select(['id', 'still', 'name'])
                    ->where('name', 'LIKE', $search)
                    ->oldest('name')
                    ->take(10)
                    ->get()
                    ->toArray();
        }

        return view('livewire.quick-search-dropdown', [
            'search_results' => $searchResults,
        ]);
    }
}

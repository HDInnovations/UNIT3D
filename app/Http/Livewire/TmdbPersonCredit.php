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

use App\Enums\Occupation;
use App\Models\PersonalFreeleech;
use App\Models\TmdbMovie;
use App\Models\TmdbPerson;
use App\Models\TmdbTv;
use App\Models\Torrent;
use App\Models\User;
use App\Traits\TorrentMeta;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class TmdbPersonCredit extends Component
{
    use TorrentMeta;
    use WithPagination;

    public TmdbPerson $person;

    #TODO: Update URL attributes once Livewire 3 fixes upstream bug. See: https://github.com/livewire/livewire/discussions/7746

    #[Url(history: true)]
    public ?int $occupationId = null;

    final public function mount(): void
    {
        $this->occupationId ??= match (true) {
            0 < $this->createdCount            => Occupation::CREATOR->value,
            0 < $this->directedCount           => Occupation::DIRECTOR->value,
            0 < $this->writtenCount            => Occupation::WRITER->value,
            0 < $this->producedCount           => Occupation::PRODUCER->value,
            0 < $this->composedCount           => Occupation::COMPOSER->value,
            0 < $this->cinematographedCount    => Occupation::CINEMATOGRAPHER->value,
            0 < $this->editedCount             => Occupation::EDITOR->value,
            0 < $this->productionDesignedCount => Occupation::PRODUCTION_DESIGNER->value,
            0 < $this->artDirectedCount        => Occupation::ART_DIRECTOR->value,
            0 < $this->actedCount              => Occupation::ACTOR->value,
            default                            => null,
        };
    }

    final protected bool $personalFreeleech {
        get => PersonalFreeleech::query()->where('user_id', '=', auth()->id())->exists();
    }

    /**
     * Livewire doesn't support enum properties, so we have to convert it manually.
     *
     * @param-out Occupation $value
     */
    public function updatingOccupation(int|string &$value): void
    {
        $value = Occupation::from($value);
    }

    final protected int $directedCount {
        get => $this->person->directedMovies()->count() + $this->person->directedTv()->count();
    }

    final protected int $createdCount {
        get => $this->person->createdTv()->count();
    }

    final protected int $writtenCount {
        get => $this->person->writtenMovies()->count() + $this->person->writtenTv()->count();
    }

    final protected int $producedCount {
        get => $this->person->producedMovies()->count() + $this->person->producedTv()->count();
    }

    final protected int $composedCount {
        get => $this->person->composedMovies()->count() + $this->person->composedTv()->count();
    }

    final protected int $cinematographedCount {
        get => $this->person->cinematographedMovies()->count() + $this->person->cinematographedTv()->count();
    }

    final protected int $editedCount {
        get => $this->person->editedMovies()->count() + $this->person->editedTv()->count();
    }

    final protected int $productionDesignedCount {
        get => $this->person->productionDesignedMovies()->count() + $this->person->productionDesignedTv()->count();
    }

    final protected int $artDirectedCount {
        get => $this->person->artDirectedMovies()->count() + $this->person->artDirectedTv()->count();
    }

    final protected int $actedCount {
        get => $this->person->actedMovies()->count() + $this->person->actedTv()->count();
    }

    /**
     * @var LengthAwarePaginator<int, TmdbMovie|TmdbTv|null>
     */
    final protected LengthAwarePaginator $medias {
        get {
            if ($this->occupationId === null) {
                return new LengthAwarePaginator([], 0, 25);
            }

            $groups = Torrent::query()
                ->where(
                    fn ($query) => $query
                        ->whereHas(
                            'movie.credits',
                            fn ($query) => $query
                                ->where('tmdb_person_id', '=', $this->person->id)
                                ->where('occupation_id', '=', $this->occupationId)
                        )
                        ->orWhereHas(
                            'tv.credits',
                            fn ($query) => $query
                                ->where('tmdb_person_id', '=', $this->person->id)
                                ->where('occupation_id', '=', $this->occupationId)
                        )
                )
                ->select('tmdb_movie_id', 'tmdb_tv_id')
                ->groupBy('tmdb_movie_id', 'tmdb_tv_id')
                ->orderByDesc(
                    TmdbMovie::query()
                        ->select('release_date')
                        ->whereColumn('torrents.tmdb_movie_id', '=', 'id')
                        ->unionAll(
                            TmdbTv::query()
                                ->select('first_air_date')
                                ->whereColumn('torrents.tmdb_tv_id', '=', 'id')
                        )
                )
                ->paginate(25);

            $movieIds = $groups->getCollection()->whereNotNull('tmdb_movie_id')->pluck('tmdb_movie_id');
            $tvIds = $groups->getCollection()->whereNotNull('tmdb_tv_id')->pluck('tmdb_tv_id');

            $movies = TmdbMovie::query()->with('genres', 'directors')->whereIntegerInRaw('id', $movieIds)->get()->keyBy('id');
            $tv = TmdbTv::query()->with('genres', 'creators')->whereIntegerInRaw('id', $tvIds)->get()->keyBy('id');

            $torrents = Torrent::query()
                ->with('type:id,name,position', 'resolution:id,name,position')
                ->select([
                    'id',
                    'name',
                    'info_hash',
                    'size',
                    'leechers',
                    'seeders',
                    'times_completed',
                    'category_id',
                    'user_id',
                    'season_number',
                    'episode_number',
                    'tmdb_movie_id',
                    'tmdb_tv_id',
                    'free',
                    'doubleup',
                    'highspeed',
                    'sticky',
                    'internal',
                    'created_at',
                    'bumped_at',
                    'type_id',
                    'resolution_id',
                    'personal_release',
                ])
                ->selectRaw(self::META_TYPE_CASE_MOVIE_TV.' as meta')
                ->withCount([
                    'comments',
                ])
                ->when(
                    !config('announce.external_tracker.is_enabled'),
                    fn ($query) => $query->withCount([
                        'seeds'   => fn ($query) => $query->where('active', '=', true)->where('visible', '=', true),
                        'leeches' => fn ($query) => $query->where('active', '=', true)->where('visible', '=', true),
                    ]),
                )
                ->withExists([
                    'featured as featured',
                    'freeleechTokens'    => fn ($query) => $query->where('user_id', '=', auth()->id()),
                    'bookmarks'          => fn ($query) => $query->where('user_id', '=', auth()->id()),
                    'history as seeding' => fn ($query) => $query->where('user_id', '=', auth()->id())
                        ->where('active', '=', 1)
                        ->where('seeder', '=', 1),
                    'history as leeching' => fn ($query) => $query->where('user_id', '=', auth()->id())
                        ->where('active', '=', 1)
                        ->where('seeder', '=', 0),
                    'history as completed' => fn ($query) => $query->where('user_id', '=', auth()->id())
                        ->where('active', '=', 0)
                        ->where('seeder', '=', 1),
                    'trump',
                ])
                ->where(
                    fn ($query) => $query
                        ->whereIn('tmdb_movie_id', $movieIds)
                        ->orWhereIn('tmdb_tv_id', $tvIds)
                )
                ->get();

            $groupedTorrents = self::groupTorrents($torrents);

            $medias = $groups->through(function ($group) use ($groupedTorrents, $movies, $tv) {
                switch (true) {
                    case $group->tmdb_movie_id !== null:
                        if ($movies->has($group->tmdb_movie_id)) {
                            $media = $movies[$group->tmdb_movie_id];
                            $media->setAttribute('meta', 'movie');
                            $media->setRelation('torrents', $groupedTorrents['movie'][$group->tmdb_movie_id] ?? []);
                            $media->setAttribute('category_id', $media->torrents['category_id']);
                        } else {
                            $media = null;
                        }

                        break;
                    case $group->tmdb_tv_id !== null:
                        if ($tv->has($group->tmdb_tv_id)) {
                            $media = $tv[$group->tmdb_tv_id];
                            $media->setAttribute('meta', 'tv');
                            $media->setRelation('torrents', $groupedTorrents['tv'][$group->tmdb_tv_id] ?? []);
                            $media->setAttribute('category_id', $media->torrents['category_id']);
                        } else {
                            $media = null;
                        }

                        break;
                    default:
                        $media = null;
                }

                return $media;
            });

            return $medias;
        }
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.tmdb-person-credit', [
            'user'                    => User::query()->with(['group'])->findOrFail(auth()->user()->id),
            'personalFreeleech'       => $this->personalFreeleech,
            'medias'                  => $this->medias,
            'directedCount'           => $this->directedCount,
            'createdCount'            => $this->createdCount,
            'writtenCount'            => $this->writtenCount,
            'producedCount'           => $this->producedCount,
            'composedCount'           => $this->composedCount,
            'cinematographedCount'    => $this->cinematographedCount,
            'editedCount'             => $this->editedCount,
            'productionDesignedCount' => $this->productionDesignedCount,
            'artDirectedCount'        => $this->artDirectedCount,
            'actedCount'              => $this->actedCount,
        ]);
    }
}

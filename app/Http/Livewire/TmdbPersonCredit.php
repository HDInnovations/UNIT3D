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
use App\Models\TmdbPerson;
use App\Models\Torrent;
use App\Models\User;
use App\Traits\TorrentMeta;
use Livewire\Attributes\Url;
use Livewire\Component;

class TmdbPersonCredit extends Component
{
    use TorrentMeta;

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
        get => cache()->get('personal_freeleech:'.auth()->user()->id) ?? false;
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
     * @var \Illuminate\Support\Collection<int, Torrent>
     */
    final protected \Illuminate\Support\Collection $medias {
        get {
            if ($this->occupationId === null) {
                return collect();
            }

            $movies = $this->person
                ->movie()
                ->with('genres', 'directors')
                ->wherePivot('occupation_id', '=', $this->occupationId)
                ->orderBy('release_date')
                ->get()
                // Since the credits table unique index has nullable columns, we get duplicate credits, which means duplicate movies
                ->unique();
            $tv = $this->person
                ->tv()
                ->with('genres', 'creators')
                ->wherePivot('occupation_id', '=', $this->occupationId)
                ->orderBy('first_air_date')
                ->get()
                // Since the credits table unique index has nullable columns, we get duplicate credits, which means duplicate tv
                ->unique();

            $movieIds = $movies->pluck('id');
            $tvIds = $tv->pluck('id');

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
                ->selectRaw(<<<'SQL'
                CASE
                    WHEN category_id IN (SELECT `id` from `categories` where `movie_meta` = 1) THEN 'movie'
                    WHEN category_id IN (SELECT `id` from `categories` where `tv_meta` = 1) THEN 'tv'
                END as meta
            SQL)
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
                        ->where(
                            fn ($query) => $query
                                ->whereRelation('category', 'movie_meta', '=', true)
                                ->whereIntegerInRaw('tmdb_movie_id', $movieIds)
                        )
                        ->orWhere(
                            fn ($query) => $query
                                ->whereRelation('category', 'tv_meta', '=', true)
                                ->whereIntegerInRaw('tmdb_tv_id', $tvIds)
                        )
                )
                ->get();

            $groupedTorrents = self::groupTorrents($torrents);

            $medias = collect();

            foreach ($movies as $movie) {
                if (\array_key_exists('movie', $groupedTorrents) && \array_key_exists($movie->id, $groupedTorrents['movie'])) {
                    $media = $movie;
                    $media->setAttribute('meta', 'movie');
                    $media->setRelation('torrents', $groupedTorrents['movie'][$movie->id]);
                    $media->setAttribute('category_id', $media->torrents['category_id']);
                    $medias->add($media);
                }
            }

            foreach ($tv as $show) {
                if (\array_key_exists('tv', $groupedTorrents) && \array_key_exists($show->id, $groupedTorrents['tv'])) {
                    $media = $show;
                    $media->setAttribute('meta', 'tv');
                    $media->setRelation('torrents', $groupedTorrents['tv'][$show->id]);
                    $media->setAttribute('category_id', $media->torrents['category_id']);
                    $medias->add($media);
                }
            }

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

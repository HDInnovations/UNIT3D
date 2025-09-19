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

namespace App\Http\Controllers;

use App\Enums\ModerationStatus;
use App\Helpers\Bencode;
use App\Helpers\MediaInfo;
use App\Helpers\TorrentHelper;
use App\Helpers\TorrentTools;
use App\Http\Requests\StoreTorrentRequest;
use App\Http\Requests\UpdateTorrentRequest;
use App\Models\Category;
use App\Models\Distributor;
use App\Models\Keyword;
use App\Models\Region;
use App\Models\Resolution;
use App\Models\Scopes\ApprovedScope;
use App\Models\Torrent;
use App\Models\TorrentFile;
use App\Models\Type;
use App\Models\User;
use App\Notifications\TorrentDeleted;
use App\Repositories\ChatRepository;
use App\Services\Igdb\IgdbScraper;
use App\Services\Tmdb\TMDBScraper;
use App\Services\Unit3dAnnounce;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Intervention\Image\Facades\Image;
use Exception;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use ReflectionException;
use JsonException;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\TorrentControllerTest
 */
class TorrentController extends Controller
{
    /**
     * TorrentController Constructor.
     */
    public function __construct(private readonly ChatRepository $chatRepository)
    {
    }

    /**
     * Display a listing of the Torrent resource.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('torrent.index');
    }

    /**
     * Display The Torrent resource.
     *
     * @throws JsonException
     * @throws \MarcReichel\IGDBLaravel\Exceptions\MissingEndpointException
     * @throws ReflectionException
     * @throws \MarcReichel\IGDBLaravel\Exceptions\InvalidParamsException
     */
    public function show(Request $request, int|string $id): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $user = $request->user()->load(['group', 'playlists']);

        $torrent = Torrent::query()
            ->withoutGlobalScope(ApprovedScope::class)
            ->with([
                'user.group',
                'comments',
                'category',
                'featured',
                'files',
                'game' => [
                    'genres',
                    'companies',
                    'platforms',
                ],
                'history' => fn ($query) => $query->where('user_id', '=', $user->id),
                'keywords',
                'movie' => [
                    'genres',
                    'credits' => ['person', 'occupation'],
                    'companies',
                    'collections.movies' => fn ($query) => $query->withMin('torrents', 'category_id')->has('torrents'),
                    'recommendedMovies'  => fn ($query) => $query->withMin('torrents', 'category_id')->has('torrents'),
                ],
                'playlists',
                'resolution',
                'subtitles',
                'type',
                'tv' => [
                    'genres',
                    'credits' => ['person', 'occupation'],
                    'companies',
                    'networks',
                    'recommendedTv' => fn ($query) => $query->withMin('torrents', 'category_id')->has('torrents'),
                ],
            ])
            ->withCount([
                'bookmarks',
                'files',
                'thanks',
                'seeds'   => fn ($query) => $query->where('active', '=', true)->where('visible', '=', true),
                'leeches' => fn ($query) => $query->where('active', '=', true)->where('visible', '=', true),
            ])
            ->withExists([
                'featured as featured',
                'bookmarks'       => fn ($query) => $query->where('user_id', '=', $user->id),
                'freeleechTokens' => fn ($query) => $query->where('user_id', '=', $user->id),
                'trump',
                'history as seeding' => fn ($query) => $query->where('user_id', '=', $user->id)
                    ->where('active', '=', true)
                    ->where('seeder', '=', true),
                'history as leeching' => fn ($query) => $query->where('user_id', '=', $user->id)
                    ->where('active', '=', true)
                    ->where('seeder', '=', false),
                'history as completed' => fn ($query) => $query->where('user_id', '=', $user->id)
                    ->where('active', '=', false)
                    ->where('seeder', '=', true),
                'resurrections' => fn ($query) => $query->where('rewarded', '=', false),
            ])
            ->withSum('tips as total_tips', 'bon')
            ->withSum(['tips as user_tips' => fn ($query) => $query->where('sender_id', '=', $user->id)], 'bon')
            ->withMax(['history' => fn ($query) => $query->where('seeder', '=', true)], 'updated_at')
            ->when(
                $user->group->is_modo,
                fn ($query) => $query
                    ->with([
                        'audits.user.group',
                        'downloads' => fn ($query) => $query->latest()->with('user.group'),
                        'reports',
                    ])
                    ->withCount([
                        'downloads',
                    ])
            )
            ->when(
                $user->group->is_torrent_modo,
                fn ($query) => $query->with([
                    'moderated.group',
                ])
            )
            ->findOrFail($id);

        $fileTree = [];

        foreach ($torrent->files->sortBy('name') as $index => $file) {
            $parts = explode('/', trim($file->name, '/'));

            $current = &$fileTree;

            for ($i = 0; $i < \count($parts) - 1; $i++) {
                $part = $parts[$i];

                /** @phpstan-ignore function.impossibleType (PHPStan doesn't recognize that $current might not be empty in subsequent loops)*/
                if (!\array_key_exists($part, $current)) {
                    $current[$part] = [
                        'type'     => 'directory',
                        'children' => [],
                    ];
                }

                $current = &$current[$part]['children'];
            }

            $current[$parts[$i]] = [
                'type' => 'file',
                'size' => $file->size,
            ];
        }

        $calculateTotals = function (array &$children) use (&$calculateTotals): array {
            $totalSize = 0;
            $totalCount = 0;

            foreach ($children as &$child) {
                if ($child['type'] === 'directory') {
                    [$child['size'], $child['count']] = $calculateTotals($child['children']);
                }

                $totalSize += $child['size'];
                $totalCount += $child['count'] ?? 1;
            }

            return [$totalSize, $totalCount];
        };

        $calculateTotals($fileTree);

        return view('torrent.show', [
            'torrent' => $torrent,
            'user'    => $user,
            'canEdit' => $user->group->is_editor
                || $user->group->is_modo
                || (
                    $user->id === $torrent->user_id
                    && (
                        $torrent->status !== ModerationStatus::APPROVED
                        || now()->isBefore($torrent->created_at->addDay())
                    )
                ),
            'personal_freeleech' => cache()->get('personal_freeleech:'.$user->id),
            'mediaInfo'          => $torrent->mediainfo !== null ? (new MediaInfo())->parse($torrent->mediainfo) : null,
            'fileTree'           => $fileTree,
        ]);
    }

    /**
     * Show the form for editing the specified Torrent resource.
     */
    public function edit(Request $request, int $id): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $user = $request->user();
        $torrent = Torrent::withoutGlobalScope(ApprovedScope::class)->findOrFail($id);

        abort_unless($user->group->is_editor || $user->group->is_modo || $user->id === $torrent->user_id, 403);

        return view('torrent.edit', [
            'categories' => Category::query()
                ->orderBy('position')
                ->get()
                ->mapWithKeys(fn ($cat) => [
                    $cat['id'] => [
                        'name' => $cat['name'],
                        'type' => match (true) {
                            $cat->movie_meta => 'movie',
                            $cat->tv_meta    => 'tv',
                            $cat->game_meta  => 'game',
                            $cat->music_meta => 'music',
                            $cat->no_meta    => 'no',
                            default          => 'no',
                        },
                    ]
                ]),
            'types'        => Type::orderBy('position')->get()->mapWithKeys(fn ($type) => [$type['id'] => ['name' => $type['name']]]),
            'resolutions'  => Resolution::orderBy('position')->get(),
            'regions'      => Region::orderBy('position')->get(),
            'distributors' => Distributor::orderBy('name')->get(),
            'keywords'     => Keyword::where('torrent_id', '=', $torrent->id)->pluck('name'),
            'torrent'      => $torrent,
            'user'         => $user,
        ]);
    }

    /**
     * Update the specified Torrent resource in storage.
     */
    public function update(UpdateTorrentRequest $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
        $torrent = Torrent::withoutGlobalScope(ApprovedScope::class)->findOrFail($id);

        abort_unless(
            $user->group->is_editor
            || $user->group->is_modo
            || (
                $user->id === $torrent->user_id
                && (
                    $torrent->status !== ModerationStatus::APPROVED
                    || now()->isBefore($torrent->created_at->addDay())
                )
            ),
            403
        );

        $torrent->update($request->validated());

        // Cover Image for No-Meta Torrents
        if ($request->hasFile('torrent-cover')) {
            $image_cover = $request->file('torrent-cover');

            abort_if(\is_array($image_cover), 400);

            $filename_cover = 'torrent-cover_'.$torrent->id.'.jpg';
            $path_cover = Storage::disk('torrent-covers')->path($filename_cover);
            Image::make($image_cover->getRealPath())->fit(400, 600)->encode('jpg', 90)->save($path_cover);
        }

        // Banner Image for No-Meta Torrents
        if ($request->hasFile('torrent-banner')) {
            $image_cover = $request->file('torrent-banner');

            abort_if(\is_array($image_cover), 400);

            $filename_cover = 'torrent-banner_'.$torrent->id.'.jpg';
            $path_cover = Storage::disk('torrent-banners')->path($filename_cover);
            Image::make($image_cover->getRealPath())->fit(960, 540)->encode('jpg', 90)->save($path_cover);
        }

        // Torrent Keywords System
        Keyword::where('torrent_id', '=', $torrent->id)->delete();

        $keywords = [];

        foreach (TorrentTools::parseKeywords($request->string('keywords')) as $keyword) {
            $keywords[] = ['torrent_id' => $torrent->id, 'name' => $keyword];
        }

        foreach (collect($keywords)->chunk(65_000 / 2) as $keywords) {
            Keyword::upsert($keywords->toArray(), ['torrent_id', 'name']);
        }

        // Meta

        match (true) {
            $torrent->tmdb_tv_id !== null    => new TMDBScraper()->tv($torrent->tmdb_tv_id),
            $torrent->tmdb_movie_id !== null => new TMDBScraper()->movie($torrent->tmdb_movie_id),
            $torrent->igdb !== null          => new IgdbScraper()->game($torrent->igdb),
            default                          => null,
        };

        return to_route('torrents.show', ['id' => $id])
            ->with('success', 'Successfully Edited!');
    }

    /**
     * Delete A Torrent.
     *
     * @throws Exception
     */
    public function destroy(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'message' => [
                'required',
                'min:1',
            ],
        ]);

        $user = $request->user();
        $torrent = Torrent::withoutGlobalScope(ApprovedScope::class)->findOrFail($id);

        abort_unless($user->group->is_modo || ($user->id === $torrent->user_id && Carbon::now()->lt($torrent->created_at->addDay())), 403);

        Notification::send(
            User::query()->whereHas('history', fn ($query) => $query->where('torrent_id', '=', $torrent->id))->get(),
            new TorrentDeleted($torrent, $request->message),
        );

        // Reset Requests
        $torrent->requests()->whereNull('approved_when')->update([
            'torrent_id' => null,
        ]);

        //Remove Torrent related info
        cache()->forget(\sprintf('torrent:%s', $torrent->info_hash));

        $torrent->comments()->delete();
        $torrent->peers()->delete();
        $torrent->history()->delete();
        $torrent->hitrun()->delete();
        $torrent->files()->delete();
        $torrent->playlists()->detach();
        $torrent->subtitles()->delete();
        $torrent->resurrections()->delete();
        $torrent->featured()->delete();

        $freeleechTokens = $torrent->freeleechTokens();

        foreach ($freeleechTokens->get() as $freeleechToken) {
            cache()->forget('freeleech_token:'.$freeleechToken->user_id.':'.$torrent->id);
        }

        $freeleechTokens->delete();

        cache()->forget('announce-torrents:by-infohash:'.$torrent->info_hash);

        Unit3dAnnounce::removeTorrent($torrent);

        $torrent->delete();

        return to_route('torrents.index')
            ->with('success', 'Torrent Has Been Deleted!');
    }

    /**
     * Torrent Upload Form.
     */
    public function create(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $user = $request->user();
        abort_unless($user->can_upload ?? $user->group->can_upload, 403, __('torrent.cant-upload').' '.__('torrent.cant-upload-desc'));

        return view('torrent.create', [
            'categories' => Category::orderBy('position')
                ->get()
                ->mapWithKeys(fn ($category) => [$category->id => [
                    'name' => $category->name,
                    'type' => match (true) {
                        $category->movie_meta => 'movie',
                        $category->tv_meta    => 'tv',
                        $category->game_meta  => 'game',
                        $category->music_meta => 'music',
                        $category->no_meta    => 'no',
                        default               => 'no',
                    },
                ]])
                ->toArray(),
            'types'        => Type::orderBy('position')->get(),
            'resolutions'  => Resolution::orderBy('position')->get(),
            'regions'      => Region::orderBy('position')->get(),
            'distributors' => Distributor::orderBy('name')->get(),
            'user'         => $request->user(),
            'category_id'  => $request->category_id ?? Category::query()->first()->id,
            'title'        => urldecode((string) $request->title),
            'imdb'         => $request->imdb,
            'movieId'      => $request->tmdb_movie_id,
            'tvId'         => $request->tmdb_tv_id,
            'mal'          => $request->mal,
            'tvdb'         => $request->tvdb,
            'igdb'         => $request->igdb,
        ]);
    }

    /**
     * Upload A Torrent.
     */
    public function store(StoreTorrentRequest $request): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->can_upload ?? $user->group->can_upload, 403, __('torrent.cant-upload').' '.__('torrent.cant-upload-desc'));

        abort_if(\is_array($request->file('torrent')), 400);

        abort_if(\is_array($request->file('nfo')), 400);

        $decodedTorrent = TorrentTools::normalizeTorrent($request->file('torrent'));

        $meta = Bencode::get_meta($decodedTorrent);

        $fileName = uniqid('', true).'.torrent'; // Generate a unique name
        Storage::disk('torrent-files')->put($fileName, Bencode::bencode($decodedTorrent));

        $torrent = Torrent::create([
            'mediainfo'    => TorrentTools::anonymizeMediainfo($request->filled('mediainfo') ? $request->string('mediainfo') : null),
            'info_hash'    => Bencode::get_infohash($decodedTorrent),
            'file_name'    => $fileName,
            'num_file'     => $meta['count'],
            'folder'       => Bencode::get_name($decodedTorrent),
            'size'         => $meta['size'],
            'nfo'          => $request->hasFile('nfo') ? TorrentTools::getNfo($request->file('nfo')) : '',
            'user_id'      => $user->id,
            'moderated_at' => now(),
            'moderated_by' => User::SYSTEM_USER_ID,
        ] + $request->safe()->except(['torrent']));

        // Populate the status/seeders/leechers/times_completed fields for the external tracker
        $torrent->refresh();

        $category = Category::findOrFail($request->integer('category_id'));

        // Backup the files contained in the torrent
        $files = TorrentTools::getTorrentFiles($decodedTorrent);

        foreach ($files as &$file) {
            $file['torrent_id'] = $torrent->id;
        }

        // Can't insert them all at once since some torrents have more files than mysql supports placeholders.
        // Divide by 3 since we're inserting 3 fields: name, size and torrent_id
        foreach (collect($files)->chunk(intdiv(65_000, 3)) as $files) {
            TorrentFile::insert($files->toArray());
        }

        // Cover Image for No-Meta Torrents
        if ($request->hasFile('torrent-cover')) {
            $image_cover = $request->file('torrent-cover');

            abort_if(\is_array($image_cover), 400);

            $filename_cover = 'torrent-cover_'.$torrent->id.'.jpg';
            $path_cover = Storage::disk('torrent-covers')->path($filename_cover);
            Image::make($image_cover->getRealPath())->fit(400, 600)->encode('jpg', 90)->save($path_cover);
        }

        // Banner Image for No-Meta Torrents
        if ($request->hasFile('torrent-banner')) {
            $image_cover = $request->file('torrent-banner');

            abort_if(\is_array($image_cover), 400);

            $filename_cover = 'torrent-banner_'.$torrent->id.'.jpg';
            $path_cover = Storage::disk('torrent-banners')->path($filename_cover);
            Image::make($image_cover->getRealPath())->fit(960, 540)->encode('jpg', 90)->save($path_cover);
        }

        // Tracker updates come after initial database updates in case tracker's offline

        Unit3dAnnounce::addTorrent($torrent);

        // Metadata updates come after tracker updates in case TMDB or IGDB is offline

        // Meta
        match (true) {
            $torrent->tmdb_tv_id !== null    => new TMDBScraper()->tv($torrent->tmdb_tv_id),
            $torrent->tmdb_movie_id !== null => new TMDBScraper()->movie($torrent->tmdb_movie_id),
            $torrent->igdb !== null          => new IgdbScraper()->game($torrent->igdb),
            default                          => null,
        };

        // Torrent Keywords System
        $keywords = [];

        foreach (TorrentTools::parseKeywords($request->string('keywords')) as $keyword) {
            $keywords[] = ['torrent_id' => $torrent->id, 'name' => $keyword];
        }

        foreach (collect($keywords)->chunk(intdiv(65_000, 2)) as $keywords) {
            Keyword::upsert($keywords->toArray(), ['torrent_id', 'name']);
        }

        // check for trusted user and update torrent
        if ($user->group->is_trusted && !$request->boolean('mod_queue_opt_in')) {
            $appurl = config('app.url');
            $user = $torrent->user;
            $username = $user->username;
            $anon = $torrent->anon;

            // Announce To Shoutbox
            if (!$anon) {
                $this->chatRepository->systemMessage(
                    \sprintf('User [url=%s/users/', $appurl).$username.']'.$username.\sprintf('[/url] has uploaded a new '.$torrent->category->name.'. [url=%s/torrents/', $appurl).$torrent->id.']'.$torrent->name.'[/url], grab it now!'
                );
            } else {
                $this->chatRepository->systemMessage(
                    \sprintf('An anonymous user has uploaded a new '.$torrent->category->name.'. [url=%s/torrents/', $appurl).$torrent->id.']'.$torrent->name.'[/url], grab it now!'
                );
            }

            if ($torrent->free >= 1) {
                $this->chatRepository->systemMessage(
                    \sprintf('Ladies and Gents, [url=%s/torrents/', $appurl).$torrent->id.']'.$torrent->name.'[/url] has been granted '.$torrent->free.'% FreeLeech! Grab It While You Can!'
                );
            }

            TorrentHelper::approveHelper($torrent->id);
        }

        return to_route('download_check', ['id' => $torrent->id])
            ->with('success', 'Your torrent file is ready to be downloaded and seeded!');
    }
}

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

namespace App\Helpers;

use App\Achievements\UserMade100Uploads;
use App\Achievements\UserMade200Uploads;
use App\Achievements\UserMade25Uploads;
use App\Achievements\UserMade300Uploads;
use App\Achievements\UserMade400Uploads;
use App\Achievements\UserMade500Uploads;
use App\Achievements\UserMade50Uploads;
use App\Achievements\UserMade600Uploads;
use App\Achievements\UserMade700Uploads;
use App\Achievements\UserMade800Uploads;
use App\Achievements\UserMade900Uploads;
use App\Achievements\UserMadeUpload;
use App\Bots\IRCAnnounceBot;
use App\Bots\IRCAnnounceBotExternal;
use App\Enums\ModerationStatus;
use App\Models\AutomaticTorrentFreeleech;
use App\Models\TmdbMovie;
use App\Models\Scopes\ApprovedScope;
use App\Models\Torrent;
use App\Models\TmdbTv;
use App\Models\User;
use App\Notifications\NewUpload;
use App\Notifications\NewWishListNotice;
use App\Services\Unit3dAnnounce;
use Illuminate\Support\Carbon;

class TorrentHelper
{
    public static function approveHelper(int $id): void
    {
        $appurl = config('app.url');

        $torrent = Torrent::with('user')->withoutGlobalScope(ApprovedScope::class)->findOrFail($id);
        $torrent->created_at = Carbon::now();
        $torrent->bumped_at = Carbon::now();
        $torrent->status = ModerationStatus::APPROVED;
        $torrent->moderated_at = now();
        $torrent->moderated_by = (int) auth()->id();

        if (!$torrent->free) {
            $autoFreeleeches = AutomaticTorrentFreeleech::query()
                ->orderBy('position')
                ->where(fn ($query) => $query->whereNull('category_id')->orWhere('category_id', '=', $torrent->category_id))
                ->where(fn ($query) => $query->whereNull('type_id')->orWhere('type_id', '=', $torrent->type_id))
                ->where(fn ($query) => $query->whereNull('resolution_id')->orWhere('resolution_id', '=', $torrent->resolution_id))
                ->where(fn ($query) => $query->whereNull('size')->orWhere('size', '<', $torrent->size))
                ->get();

            foreach ($autoFreeleeches as $autoFreeleech) {
                if ($autoFreeleech->name_regex === null || preg_match($autoFreeleech->name_regex, $torrent->name)) {
                    $torrent->free = $autoFreeleech->freeleech_percentage;

                    break;
                }
            }
        }

        $torrent->save();

        $uploader = $torrent->user;

        switch (true) {
            case $torrent->tmdb_movie_id !== null:
                User::query()
                    ->whereRelation('wishes', 'tmdb_movie_id', '=', $torrent->tmdb_movie_id)
                    ->get()
                    ->each
                    ->notify(new NewWishListNotice($torrent));

                break;
            case $torrent->tmdb_tv_id !== null:
                User::query()
                    ->whereRelation('wishes', 'tmdb_tv_id', '=', $torrent->tmdb_tv_id)
                    ->get()
                    ->each
                    ->notify(new NewWishListNotice($torrent));

                break;
        }

        if (!$torrent->anon && $uploader !== null) {
            foreach ($uploader->followers()->get() as $follower) {
                $follower->notify(new NewUpload('follower', $torrent));
            }
        }

        $user = $torrent->user;
        $username = $user->username;
        $anon = $torrent->anon;

        if (!$anon) {
            // Achievements
            $user->unlock(new UserMadeUpload());
            $user->addProgress(new UserMade25Uploads(), 1);
            $user->addProgress(new UserMade50Uploads(), 1);
            $user->addProgress(new UserMade100Uploads(), 1);
            $user->addProgress(new UserMade200Uploads(), 1);
            $user->addProgress(new UserMade300Uploads(), 1);
            $user->addProgress(new UserMade400Uploads(), 1);
            $user->addProgress(new UserMade500Uploads(), 1);
            $user->addProgress(new UserMade600Uploads(), 1);
            $user->addProgress(new UserMade700Uploads(), 1);
            $user->addProgress(new UserMade800Uploads(), 1);
            $user->addProgress(new UserMade900Uploads(), 1);
        }

        // Announce To IRC
        if (config('irc-bot.enabled')) {
            $meta = null;
            $category = $torrent->category;

            if ($torrent->tmdb_movie_id > 0 || $torrent->tmdb_tv_id > 0) {
                $meta = match (true) {
                    $category->tv_meta    => TmdbTv::find($torrent->tmdb_tv_id),
                    $category->movie_meta => TmdbMovie::find($torrent->tmdb_movie_id),
                    default               => null,
                };
            }

            (new IRCAnnounceBot())
                ->to(config('irc-bot.channel'))
                ->say('['.config('app.name').'] '.($anon ? 'An anonymous user' : $username).' has uploaded '.$torrent->name.' grab it now!')
                ->say(
                    '[Category: '.$category->name.'] '
                    .'[Type: '.$torrent->type->name.'] '
                    .'[Size: '.$torrent->getSize().'] '
                    .'[TMDB vote average: '.($meta->vote_average ?? 0).'] '
                    .'[TMDB vote count: '.($meta->vote_count ?? 0).']'
                )
                ->say(\sprintf('[Link: %s/torrents/', $appurl).$id.']');
        }

        // Announce to external IRC service
        IRCAnnounceBotExternal::postAnnounceMsg($torrent);

        cache()->forget('announce-torrents:by-infohash:'.$torrent->info_hash);

        Unit3dAnnounce::addTorrent($torrent);
    }
}

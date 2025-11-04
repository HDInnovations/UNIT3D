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

namespace App\Bots;

use App\Models\IgdbGame;
use App\Models\TmdbMovie;
use App\Models\TmdbTv;
use App\Models\Torrent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class IRCAnnounceBotExternal
{
    public static function postAnnounceMsg(Torrent $torrent): bool
    {
        if (! config('irc-bot-external.is_enabled')) {
            return false;
        }

        $appurl = config('app.url');
        $announceTypeEnum = 0; // 0 NEW

        $originEnum = match (true) {
            $torrent->personal_release == true => 2,
            $torrent->internal                 => 1,
            default                            => 0,
        };

        $leechTypeEnum = match ($torrent->free) {
            100     => 1,
            75      => 2,
            50      => 3,
            25      => 4,
            default => 0,
        };

        if ($torrent->doubleup) {
            $leechTypeEnum = match ($leechTypeEnum) {
                0 => 5,
                1 => 6,
                2 => 7,
                3 => 8,
                4 => 9,
            };
        }

        $meta = null;
        $category = $torrent->category;

        if ($torrent->tmdb_movie_id > 0 || $torrent->tmdb_tv_id > 0) {
            $meta = match (true) {
                $category->tv_meta    => TmdbTv::find($torrent->tmdb_tv_id),
                $category->movie_meta => TmdbMovie::find($torrent->tmdb_movie_id),
                $category->game_meta  => IgdbGame::find($torrent->igdb),
                default               => null,
            };
        }

        return self::post([
            'id'                     => $torrent->id,
            'url'                    => \sprintf('%s/torrents/%d', $appurl, $torrent->id),
            'name'                   => $torrent->name,
            'uploader'               => $torrent->anon ? 'Anonymous' : $torrent->user->username,
            'size'                   => $torrent->getSize(),
            'size_bytes'             => $torrent->size,
            'announce_type_enum'     => $announceTypeEnum,
            'category_enum'          => $category->id,
            'category_name'          => $category->name,
            'origin_enum'            => $originEnum,
            'leech_type_enum'        => $leechTypeEnum,
            'upload_time_unix_epoch' => $torrent->created_at->getTimestamp(),
            'freeleech'              => $torrent->free > 0,
            'freeleech_percent'      => $torrent->free,
            'double_up'              => $torrent->doubleup,
            'resolution'             => $torrent->resolution?->name ?? '',
            'type'                   => $torrent->type->name,
            'release_year'           => $meta?->release_date?->format('Y') ?? $meta?->first_air_date?->format('Y') ?? $meta?->first_release_date?->format('Y'),
            'title'                  => $meta->title ?? $torrent->name,
            'metadata'               => [
                'tmdb_id' => $torrent->tmdb_movie_id ?? $torrent->tmdb_tv_id,
                'imdb_id' => $torrent->imdb,
                'tvdb_id' => $torrent->tvdb,
                'mal_id'  => $torrent->mal,
                'igdb_id' => $torrent->igdb,
            ],
        ]);
    }

    /**
     * @param array<mixed> $data
     */
    private static function post(array $data): bool
    {
        if (! self::isConfigValid()) {
            return false;
        }

        try {
            $response = self::buildHttpClient()->post(self::buildRoute(), $data);
        } catch (Throwable) {
            Log::error('External IRC Announce error - POST');

            return false;
        }

        if (! $response->ok()) {
            Log::notice('External IRC Announce error - POST', [
                'status' => $response->status(),
                'body'   => $response->body(),
                'data'   => $data,
            ]);

            return false;
        }

        return true;
    }

    private static function isConfigValid(): bool
    {
        return config('irc-bot-external.is_enabled') === true
            && config('irc-bot-external.channel') !== null
            && ((
                config('irc-bot-external.unix_socket') !== null
                && config('irc-bot-external.host') === null
                && config('irc-bot-external.port') === null
            ) || (
                config('irc-bot-external.unix_socket') === null
                && config('irc-bot-external.host') !== null
                && config('irc-bot-external.port') !== null
                && config('irc-bot-external.key') !== null
            ));
    }

    private static function buildRoute(): string
    {
        $channel = ltrim(config('irc-bot-external.channel'), '#');

        if (config('irc-bot-external.unix_socket') === null) {
            $route = 'http://'.config('irc-bot-external.host').':'.config('irc-bot-external.port').'/api/webhook/announce/'.$channel.'?apikey='.config('irc-bot-external.key');
        } else {
            $route = 'http://localhost/api/webhook/announce/'.$channel.'?apikey='.config('irc-bot-external.key');
        }

        return rtrim($route, '/');
    }

    private static function buildHttpClient(): \Illuminate\Http\Client\PendingRequest
    {
        $client = Http::createPendingRequest();

        if (config('irc-bot-external.unix_socket') !== null) {
            $client->withOptions([
                'curl' => [
                    CURLOPT_UNIX_SOCKET_PATH => config('irc-bot-external.unix_socket'),
                ],
            ]);
        }

        return $client;
    }
}

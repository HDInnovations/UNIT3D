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

namespace App\Services;

use App\Models\TorrentRequest;
use App\Models\TorrentRequestBounty;
use App\Models\User;
use App\Repositories\ChatRepository;
use App\Services\Igdb\IgdbScraper;
use App\Services\Tmdb\TMDBScraper;

final readonly class TorrentRequestCreator
{
    public function __construct(private ChatRepository $chatRepository)
    {
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(User $user, array $attributes): TorrentRequest
    {
        $bounty = $attributes['bounty'];
        $anon = (bool) $attributes['anon'];

        $user->decrement('seedbonus', $bounty);

        $torrentRequest = TorrentRequest::query()->create([
            'user_id'   => $user->id,
            'bumped_at' => now(),
        ] + $attributes);

        TorrentRequestBounty::query()->create([
            'user_id'     => $user->id,
            'seedbonus'   => $bounty,
            'requests_id' => $torrentRequest->id,
            'anon'        => $anon,
        ]);

        if (! $torrentRequest->anon) {
            $this->chatRepository->systemMessage(
                \sprintf('[url=%s]%s[/url] has created a new request [url=%s]%s[/url]', href_profile($user), $user->username, href_request($torrentRequest), $torrentRequest->name)
            );
        } else {
            $this->chatRepository->systemMessage(
                \sprintf('An anonymous user has created a new request [url=%s]%s[/url]', href_request($torrentRequest), $torrentRequest->name)
            );
        }

        match (true) {
            $torrentRequest->tmdb_tv_id !== null    => new TMDBScraper()->tv($torrentRequest->tmdb_tv_id),
            $torrentRequest->tmdb_movie_id !== null => new TMDBScraper()->movie($torrentRequest->tmdb_movie_id),
            $torrentRequest->igdb !== null          => new IgdbScraper()->game($torrentRequest->igdb),
            default                                 => null,
        };

        return $torrentRequest;
    }
}

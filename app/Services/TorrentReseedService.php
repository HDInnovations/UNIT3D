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

declare(strict_types=1);

namespace App\Services;

use App\Enums\TorrentReseedRequestResult;
use App\Models\History;
use App\Models\Torrent;
use App\Models\TorrentReseed;
use App\Models\User;
use App\Notifications\NewReseedRequest;
use App\Repositories\ChatRepository;

final readonly class TorrentReseedService
{
    public function __construct(private ChatRepository $chatRepository)
    {
    }

    public function request(Torrent $torrent, User $user): TorrentReseedRequestResult
    {
        $existingUserReseed = TorrentReseed::query()
            ->where('torrent_id', '=', $torrent->id)
            ->where('user_id', '=', $user->id)
            ->first();

        if ($existingUserReseed) {
            return TorrentReseedRequestResult::AlreadyRequested;
        }

        if ($torrent->seeders > 2) {
            return TorrentReseedRequestResult::Ineligible;
        }

        $existingReseed = TorrentReseed::query()->where('torrent_id', '=', $torrent->id)->first();

        if ($existingReseed) {
            $existingReseed->increment('requests_count');

            return TorrentReseedRequestResult::Counted;
        }

        TorrentReseed::query()->create([
            'torrent_id'     => $torrent->id,
            'user_id'        => $user->id,
            'requests_count' => 1,
        ]);

        $potentialReseeds = History::query()
            ->where('torrent_id', '=', $torrent->id)
            ->where('active', '=', 0)
            ->get();

        foreach ($potentialReseeds as $potentialReseed) {
            User::query()->find($potentialReseed->user_id)?->notify(new NewReseedRequest($torrent));
        }

        $torrentUrl = href_torrent($torrent);

        $this->chatRepository->systemMessage(
            \sprintf('Ladies and Gents, a reseed request was just placed on [url=%s]%s[/url] can you help out?', $torrentUrl, $torrent->name)
        );

        return TorrentReseedRequestResult::Created;
    }
}

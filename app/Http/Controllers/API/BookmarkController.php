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

namespace App\Http\Controllers\API;

use App\Models\Torrent;
use Dedoc\Scramble\Attributes\ExcludeAllRoutesFromDocs;

#[ExcludeAllRoutesFromDocs]
class BookmarkController extends BaseController
{
    final public function store(int $torrentId): bool
    {
        auth()->user()->bookmarks()->attach($torrentId);

        Torrent::query()->whereKey($torrentId)->searchable();

        return true;
    }

    final public function destroy(int $torrentId): bool
    {
        auth()->user()->bookmarks()->detach($torrentId);

        Torrent::query()->whereKey($torrentId)->searchable();

        return false;
    }
}

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

use App\Models\TorrentRequest;

describe('torrent request model', function (): void {
    it('provides a fallback category for requests whose category was deleted', function (): void {
        $torrentRequest = TorrentRequest::factory()->make([
            'category_id' => 999999,
        ]);

        expect($torrentRequest->category->name)->toBe('Deleted category');
    });
});

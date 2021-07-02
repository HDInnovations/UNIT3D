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

namespace App\Observers;

use App\Models\Torrent;

class TorrentObserver
{
    /**
     * Handle the Torrent "created" event.
     *
     *
     * @return void
     */
    public function created(Torrent $torrent)
    {
        //\cache()->put(\sprintf('torrent:%s', $torrent->info_hash), $torrent);
    }

    /**
     * Handle the Torrent "updated" event.
     *
     *
     * @return void
     */
    public function updated(Torrent $torrent)
    {
        //\cache()->put(\sprintf('torrent:%s', $torrent->info_hash), $torrent);
    }

    /**
     * Handle the Torrent "retrieved" event.
     *
     *
     * @return void
     */
    public function retrieved(Torrent $torrent)
    {
        //\cache()->add(\sprintf('torrent:%s', $torrent->info_hash), $torrent);
    }

    /**
     * Handle the Torrent "deleted" event.
     *
     *
     * @return void
     */
    public function deleted(Torrent $torrent)
    {
        //\cache()->forget(\sprintf('torrent:%s', $torrent->info_hash));
    }

    /**
     * Handle the Torrent "restored" event.
     *
     *
     * @return void
     */
    public function restored(Torrent $torrent)
    {
        //\cache()->put(\sprintf('torrent:%s', $torrent->info_hash), $torrent);
    }
}

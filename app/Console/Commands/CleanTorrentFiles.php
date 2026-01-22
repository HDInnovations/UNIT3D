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

namespace App\Console\Commands;

use App\Helpers\Bencode;
use App\Models\Torrent;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CleanTorrentFiles extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'clean:torrent_files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleans torrent files to remove extra unneeded data';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        $this->alert('Torrent file cleaning started');

        Torrent::query()->withoutGlobalScopes()->select('file_name')->orderBy('id')->chunk(100, function ($torrents): void {
            foreach ($torrents as $torrent) {
                if (Storage::disk('torrent-files')->exists($torrent->file_name)) {
                    $filePath = Storage::disk('torrent-files')->path($torrent->file_name);
                    $dict = Bencode::bdecode_file($filePath);

                    // Whitelisted keys
                    $dict = array_intersect_key($dict, [
                        'created by' => '',
                        'encoding'   => '',
                        'info'       => '',
                    ]);

                    Storage::disk('torrent-files')->put($filePath, Bencode::bencode($dict));
                }
            }
        });

        $this->alert('Torrent file cleaning complete');
    }
}

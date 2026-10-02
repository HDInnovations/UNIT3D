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
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Edition-specific normalized facts (e.g. a music release's format, a
     * book edition's language/publisher) that legitimately vary between
     * accessible variants of the same catalogue Work, so category filters
     * can match on whichever accessible edition/torrent actually has the
     * requested value instead of only the Work's last-synced snapshot.
     */
    public function up(): void
    {
        Schema::table('torrent_metadata', function (Blueprint $table): void {
            $table->json('facets')->nullable()->after('raw');
        });
    }

    public function down(): void
    {
        Schema::table('torrent_metadata', function (Blueprint $table): void {
            $table->dropColumn('facets');
        });
    }
};

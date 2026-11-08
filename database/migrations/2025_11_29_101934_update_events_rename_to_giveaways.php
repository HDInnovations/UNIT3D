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
 * @author     Obi-wana
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::rename('events', 'giveaways');
        Schema::rename('prizes', 'giveaway_prizes');
        Schema::rename('claimed_prizes', 'giveaway_claimed_prizes');

        Schema::table('giveaway_prizes', function (Blueprint $table): void {
            $table->renameColumn('event_id', 'giveaway_id');
        });
        Schema::table('giveaway_claimed_prizes', function (Blueprint $table): void {
            $table->renameColumn('event_id', 'giveaway_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('giveaways', 'events');
        Schema::rename('giveaway_prizes', 'prizes');
        Schema::rename('giveaway_claimed_prizes', 'claimed_prizes');

        Schema::table('giveaway_prizes', function (Blueprint $table): void {
            $table->renameColumn('giveaway_id', 'event_id');
        });

        Schema::table('giveaway_claimed_prizes', function (Blueprint $table): void {
            $table->renameColumn('giveaway_id', 'event_id');
        });
    }
};

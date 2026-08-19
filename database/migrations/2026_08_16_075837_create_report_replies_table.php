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

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('report_replies', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('report_id');
            $table->text('content');
            $table->unsignedInteger('user_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate();
            $table->foreign('report_id')->references('id')->on('reports')->cascadeOnUpdate()->cascadeOnDelete();
        });

        DB::table('reports')->eachById(function (object $report): void {
            if ($report->solved_by !== null) {
                DB::table('report_replies')->insert([
                    'report_id'  => $report->id,
                    'content'    => $report->message,
                    'user_id'    => $report->solved_by,
                    'created_at' => $report->solved_at,
                ]);
            }
        });

        DB::table('reports')->whereNull('assigned_to')->update([
            'assigned_to' => DB::raw('COALESCE(assigned_to, solved_by)'),
        ]);

        Schema::table('reports', function (Blueprint $table): void {
            $table->dropColumn('verdict');
            $table->dropForeign(['solved_by']);
            $table->index(['solved_at', 'assigned_to', 'snoozed_until']);
            $table->dropIndex(['solved_by', 'assigned_to', 'snoozed_until']);
            $table->dropColumn('solved_by');
            $table->boolean('user_read')->default(true);
            $table->boolean('staff_read')->default(true);
            $table->renameColumn('assigned_to', 'staff_id');
        });

        Schema::table('reports', function (Blueprint $table): void {
            $table->boolean('user_read')->default(false)->change();
            $table->boolean('staff_read')->default(false)->change();
        });
    }
};

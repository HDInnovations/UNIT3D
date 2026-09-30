<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('torrents', 'mod_queue_opt_in')) {
            return;
        }

        Schema::table('torrents', function (Blueprint $table): void {
            $table->boolean('mod_queue_opt_in')->default(false)->after('personal_release');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('torrents', 'mod_queue_opt_in')) {
            return;
        }

        Schema::table('torrents', function (Blueprint $table): void {
            $table->dropColumn('mod_queue_opt_in');
        });
    }
};

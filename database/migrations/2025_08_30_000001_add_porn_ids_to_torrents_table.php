<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('torrents', function (Blueprint $table) {
            $table->string('fansdb_id')->nullable()->after('mal');
            $table->string('stashdb_id')->nullable()->after('fansdb_id');
            $table->string('theporndb_id')->nullable()->after('stashdb_id');
        });
    }

    public function down(): void
    {
        Schema::table('torrents', function (Blueprint $table) {
            $table->dropColumn(['fansdb_id', 'stashdb_id', 'theporndb_id']);
        });
    }
};

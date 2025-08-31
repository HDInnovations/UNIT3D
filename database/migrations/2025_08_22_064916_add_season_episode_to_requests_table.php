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
        Schema::table('requests', function (Blueprint $table): void {
            $table->unsignedInteger('season_number')->after('igdb')->nullable();
            $table->unsignedInteger('episode_number')->after('season_number')->nullable();

            $table->index(['season_number', 'episode_number']);
            $table->index('episode_number');
        });
    }
};

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
        Schema::table('user_settings', function (Blueprint $table): void {
            $table->boolean('auto_freeleech_apply')->default(false)->after('unbookmark_torrents_on_completion');
            $table->unsignedInteger('auto_freeleech_min_tokens')->default(0)->after('auto_freeleech_apply');
        });
    }
};

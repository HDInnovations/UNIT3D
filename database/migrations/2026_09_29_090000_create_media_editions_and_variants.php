<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('media_editions', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('tmdb_movie_id')->nullable()->index();
            $table->unsignedInteger('tmdb_tv_id')->nullable()->index();
            $table->string('kind')->default('standard');
            $table->string('name')->nullable();
            $table->unsignedSmallInteger('release_year')->nullable();
            $table->text('provenance')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->unsignedInteger('verified_by')->nullable();
            $table->timestamps();
            $table->index(['tmdb_movie_id', 'tmdb_tv_id', 'kind']);
        });

        Schema::create('media_variants', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('media_edition_id');
            $table->string('source')->nullable();
            $table->string('container')->nullable();
            $table->string('video_codec')->nullable();
            $table->string('resolution')->nullable();
            $table->string('hdr')->nullable();
            $table->string('video_bit_rate')->nullable();
            $table->string('overall_bit_rate')->nullable();
            $table->string('duration')->nullable();
            $table->json('audio_tracks')->nullable();
            $table->json('subtitle_languages')->nullable();
            $table->timestamps();
            $table->foreign('media_edition_id')->references('id')->on('media_editions')->cascadeOnDelete();
        });

        Schema::table('torrents', function (Blueprint $table): void {
            $table->unsignedInteger('media_variant_id')->nullable()->after('igdb')->index();
            $table->foreign('media_variant_id')->references('id')->on('media_variants')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('torrents', function (Blueprint $table): void {
            $table->dropForeign(['media_variant_id']);
            $table->dropColumn('media_variant_id');
        });
        Schema::dropIfExists('media_variants');
        Schema::dropIfExists('media_editions');
    }
};

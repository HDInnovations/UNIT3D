<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('categories', 'book_meta')) {
            Schema::table('categories', function (Blueprint $table): void {
                $table->boolean('book_meta')->default(false)->after('music_meta');
            });
        }

        DB::table('categories')
            ->where('name', '=', 'Books')
            ->update(['book_meta' => true, 'no_meta' => false]);

        Schema::create('torrent_metadata', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('torrent_id')->unique();
            $table->foreign('torrent_id')->references('id')->on('torrents')->cascadeOnDelete();
            $table->string('source');
            $table->string('source_id');
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('released_on')->nullable();
            $table->string('publisher')->nullable();
            $table->unsignedSmallInteger('item_count')->nullable();
            $table->text('summary')->nullable();
            $table->string('source_url');
            $table->json('raw');
            $table->timestamps();

            $table->index(['source', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('torrent_metadata');

        Schema::table('categories', function (Blueprint $table): void {
            $table->dropColumn('book_meta');
        });
    }
};

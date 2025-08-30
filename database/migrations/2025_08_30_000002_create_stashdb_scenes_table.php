<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stashdb_scenes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('studio_id')->nullable();
            $table->string('studio_name')->nullable();
            $table->string('title');
            $table->json('urls')->nullable();
            $table->json('tags')->nullable();
            $table->date('release_date')->nullable();
            $table->json('performers')->nullable();
            $table->json('fingerprints')->nullable();
            $table->integer('duration')->nullable();
            $table->text('details')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stashdb_scenes');
    }
};

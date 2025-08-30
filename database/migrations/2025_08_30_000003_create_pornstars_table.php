<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('pornstars', function (Blueprint $table) {
            $table->id();
            $table->uuid('stashdb_id')->unique();
            $table->string('name');
            $table->json('aliases')->nullable();
            $table->integer('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('country')->nullable();
            $table->string('ethnicity')->nullable();
            $table->string('eye_color')->nullable();
            $table->string('hair_color')->nullable();
            $table->string('cup_size')->nullable();
            $table->string('band_size')->nullable();
            $table->string('waist_size')->nullable();
            $table->string('hip_size')->nullable();
            $table->string('breast_type')->nullable();
            $table->date('birth_date')->nullable();
            $table->json('birthdate')->nullable();
            $table->integer('height')->nullable();
            $table->integer('scene_count')->nullable();
            $table->integer('career_start_year')->nullable();
            $table->integer('career_end_year')->nullable();
            $table->json('images')->nullable();
            $table->json('urls')->nullable();
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('pornstars');
    }
};

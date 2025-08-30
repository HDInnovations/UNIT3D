<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('pornstar_scene', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pornstar_id')->constrained('pornstars')->onDelete('cascade');
            $table->uuid('scene_id');
            $table->foreign('scene_id')->references('id')->on('stashdb_scenes')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['pornstar_id', 'scene_id']);
        });
    }
    public function down() {
        Schema::dropIfExists('pornstar_scene');
    }
};

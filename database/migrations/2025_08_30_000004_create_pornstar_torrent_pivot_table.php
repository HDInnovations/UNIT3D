<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('pornstar_torrent', function (Blueprint $table) {
                $table->id();
                $table->string('stashid');
                $table->unsignedInteger('torrent_id');
                $table->foreign('torrent_id')->references('id')->on('torrents')->onDelete('cascade');
                $table->foreignId('pornstar_id')->constrained('pornstars')->onDelete('cascade');
                $table->timestamps();
                $table->unique(['stashid', 'pornstar_id', 'torrent_id']);
        });
    }
    public function down() {
        Schema::dropIfExists('pornstar_torrent');
    }
};

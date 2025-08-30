<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Torrent;
use App\Models\MediaHub\StashDBScene;
use App\Services\StashDB\StashDBScraper;

class ImportStashDBScenes extends Command
{
    protected $signature = 'stashdb:import-scenes';
    protected $description = 'Import StashDB scene data for torrents with stashdb_id into MediaHub';

    public function handle()
    {
        $torrents = Torrent::whereNotNull('stashdb_id')->get();
        $scraper = new StashDBScraper();
        $imported = 0;

        foreach ($torrents as $torrent) {
            $sceneData = $scraper->scene($torrent->stashdb_id);
            if ($sceneData && !StashDBScene::find($sceneData['id'])) {
                StashDBScene::create([
                    'id' => $sceneData['id'],
                    'studio_id' => $sceneData['studio']['id'] ?? null,
                    'studio_name' => $sceneData['studio']['name'] ?? null,
                    'title' => $sceneData['title'] ?? '',
                    'urls' => $sceneData['urls'] ?? [],
                    'tags' => $sceneData['tags'] ?? [],
                    'release_date' => $sceneData['release_date'] ?? null,
                    'performers' => $sceneData['performers'] ?? [],
                    'fingerprints' => $sceneData['fingerprints'] ?? [],
                    'duration' => $sceneData['duration'] ?? null,
                    'details' => $sceneData['details'] ?? null,
                    'images' => $sceneData['images'] ?? [],
                ]);
                $imported++;
            }
        }
        $this->info("Imported $imported new StashDB scenes.");
    }
}

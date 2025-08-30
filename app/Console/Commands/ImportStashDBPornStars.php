<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\StashDB\StashDBPornStarScraper;
use App\Models\MediaHub\PornStar;

class ImportStashDBPornStars extends Command
{
    protected $signature = 'stashdb:import-pornstars {ids*}';
    protected $description = 'Import StashDB performers (PornStars) by ID';

    public function handle()
    {
        $scraper = new StashDBPornStarScraper();
        foreach ($this->argument('ids') as $id) {
            $data = $scraper->performer($id);
            if ($data) {
                PornStar::updateOrCreate(
                    ['stashdb_id' => $data['id']],
                    [
                        'name' => $data['name'],
                        'aliases' => $data['aliases'],
                        'age' => $data['age'] ?? null,
                        'gender' => $data['gender'] ?? null,
                        'country' => $data['country'] ?? null,
                        'ethnicity' => $data['ethnicity'] ?? null,
                        'eye_color' => $data['eye_color'] ?? null,
                        'hair_color' => $data['hair_color'] ?? null,
                        'cup_size' => $data['cup_size'] ?? null,
                        'band_size' => $data['band_size'] ?? null,
                        'waist_size' => $data['waist_size'] ?? null,
                        'hip_size' => $data['hip_size'] ?? null,
                        'breast_type' => $data['breast_type'] ?? null,
                        'birth_date' => $data['birth_date'] ?? null,
                        'birthdate' => $data['birthdate'] ?? null,
                        'height' => $data['height'] ?? null,
                        'scene_count' => $data['scene_count'] ?? null,
                        'career_start_year' => $data['career_start_year'] ?? null,
                        'career_end_year' => $data['career_end_year'] ?? null,
                        'images' => $data['images'] ?? [],
                        'urls' => $data['urls'] ?? [],
                    ]
                );
                $this->info("Imported: {$data['name']} ({$data['id']})");
            } else {
                $this->warn("Not found: $id");
            }
        }
    }
}

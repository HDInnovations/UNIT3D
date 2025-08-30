<?php

namespace Tests\Feature;

use App\Services\StashDB\StashDBScraper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StashDBScraperTest extends TestCase
{
    /** @test */
    public function it_fetches_scene_data_by_id()
{       
        logger()->info('StashDB API Key', ['key' => config('services.stashdb.key')]);
        $scraper = new StashDBScraper();
        $sceneId = '9b8caadb-4232-4ec4-bd0a-5fae27c1c55b'; // Example ID
        $scene = $scraper->scene($sceneId);

        $this->assertIsArray($scene);
        $this->assertEquals($sceneId, $scene['id'] ?? null);
        $this->assertArrayHasKey('title', $scene);
        $this->assertArrayHasKey('studio', $scene);
        $this->assertArrayHasKey('tags', $scene);
        $this->assertArrayHasKey('performers', $scene);
    }
}

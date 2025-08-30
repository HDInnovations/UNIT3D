<?php
namespace App\Services\StashDB;

use Illuminate\Support\Facades\Http;

class StashDBPornStarScraper
{
    protected string $endpoint = 'https://stashdb.org/graphql';

    public function performer(string $id)
    {
        $query = <<<GRAPHQL
        query PerformerQuery {
          findPerformer(id: "$id") {
            id
            name
            aliases
            age
            gender
            country
            ethnicity
            eye_color
            hair_color
            cup_size
            band_size
            waist_size
            hip_size
            breast_type
            birth_date
            birthdate { accuracy date }
            height
            scene_count
            career_start_year
            career_end_year
            images { id url }
            urls { type url }
          }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'ApiKey' => config('services.stashdb.key'),
            'Content-Type' => 'application/json',
        ])->post($this->endpoint, [
            'query' => $query,
        ]);

        $data = $response->json();
        return $data['data']['findPerformer'] ?? null;
    }
}

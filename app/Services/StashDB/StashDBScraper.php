<?php

namespace App\Services\StashDB;

use Illuminate\Support\Facades\Http;

class StashDBScraper
{
    protected string $endpoint = 'https://stashdb.org/graphql';

    public function scene(string $id)
    {
        $query = <<<GRAPHQL
        query MyQuery {
          findScene(id: "$id") {
            id
            studio { id name }
            title
            urls { type url }
            tags { name }
            release_date
            performers { performer { id name } }
            fingerprints { algorithm hash reports }
            duration
            details
            images { id url }
          }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'ApiKey' => config('services.stashdb.key'),
            'Content-Type' => 'application/json',
        ])->post($this->endpoint, [
            'query' => $query,
        ]);

        // Debug logging: log request and response
        logger()->info('StashDB API request', [
            'endpoint' => $this->endpoint,
            'headers' => [
                'ApiKey' => config('services.stashdb.key'),
                'Content-Type' => 'application/json',
            ],
            'body' => [
                'query' => $query,
            ],
        ]);
        logger()->info('StashDB API raw response', [
            'status' => $response->status(),
            'body' => $response->body(),
            'json' => $response->json(),
        ]);

        $data = $response->json();
        if (!isset($data['data']['findScene'])) {
            // Debug: log or dump the full response for troubleshooting
            logger()->error('StashDB API response', $data);
            // Uncomment below for direct output during testing
            // dd($data);
        }
        return $data['data']['findScene'] ?? null;
    }
}

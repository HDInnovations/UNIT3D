<?php

declare(strict_types=1);

use App\Enums\UserGroup;
use App\Models\Article;
use App\Models\User;
use Illuminate\Routing\Middleware\ThrottleRequestsWithRedis;

test('articles rss feed returns the latest articles', function (): void {
    $this->withoutMiddleware(ThrottleRequestsWithRedis::class);

    $user = User::factory()->create([
        'group_id' => UserGroup::USER->value,
    ]);

    $olderArticle = Article::factory()->create([
        'title'      => 'Older tracker announcement',
        'content'    => 'Older update',
        'created_at' => now()->subDay(),
        'updated_at' => now()->subDay(),
    ]);

    $latestArticle = Article::factory()->create([
        'title'      => 'Latest tracker announcement',
        'content'    => '[b]Important[/b] tracker update',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this->get(route('rss.articles.rsskey', ['rsskey' => $user->rsskey]));

    $response->assertOk();
    $response->assertSee('<rss version="2.0"', false);
    $response->assertSee(route('articles.show', ['article' => $latestArticle]), false);
    $response->assertSee(route('articles.show', ['article' => $olderArticle]), false);
    $response->assertSeeInOrder([
        'Latest tracker announcement',
        'Older tracker announcement',
    ]);
});

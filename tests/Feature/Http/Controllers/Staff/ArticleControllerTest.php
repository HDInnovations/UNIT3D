<?php

declare(strict_types=1);

/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

use App\Models\Article;

test('create returns an ok response', function (): void {
    $response = $this->get(route('staff.articles.create'));
    $response->assertOk();
    $response->assertViewIs('Staff.article.create');
});

test('destroy returns an ok response', function (): void {
    $article = Article::factory()->create();

    $this->delete(route('staff.articles.destroy', [$article]))
        ->assertRedirect(route('staff.articles.index'))
        ->assertSessionHas('success', 'Article has successfully been deleted');

    $this->assertModelMissing($article);
});

test('edit returns an ok response', function (): void {
    $article = Article::factory()->create();

    $this->get(route('staff.articles.edit', [$article]))
        ->assertOk()
        ->assertViewIs('Staff.article.edit')
        ->assertViewHas('article', $article);
});

test('index returns an ok response', function (): void {
    $this->get(route('staff.articles.index'))
        ->assertOk()
        ->assertViewIs('Staff.article.index')
        ->assertViewHas('articles');
});

test('store returns an ok response', function (): void {
    $article = Article::factory()->make()->makeHidden('user_id');

    $this->post(route('staff.articles.store'), $article->toArray())
        ->assertRedirect(route('staff.articles.index'))
        ->assertSessionHas('success', 'Your article has successfully published!');

    $this->assertDatabaseHas('articles', $article->toArray());
});

test('update returns an ok response', function (): void {
    $article = Article::factory()->create();
    $newArticle = Article::factory()->make()->makeHidden('user_id');

    $this->post(route('staff.articles.update', [$article]), $newArticle->toArray())
        ->assertRedirect(route('staff.articles.index'))
        ->assertSessionHas('success', 'Your article changes have successfully published!');

    $this->assertDatabaseHas('articles', [
        'id' => $article->id,
        ...$newArticle->toArray(),
    ]);
});

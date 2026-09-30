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

use App\Http\Controllers\Staff\ArticleController;
use App\Http\Requests\Staff\StoreArticleRequest;
use App\Http\Requests\Staff\UpdateArticleRequest;
use App\Models\Article;
use App\Models\Group;
use App\Models\User;

beforeEach(function (): void {
    $this->staffUser = User::factory()->create([
        'group_id' => fn () => Group::factory()->create([
            'is_owner' => true,
            'is_admin' => true,
            'is_modo'  => true,
        ])->id,
    ]);
});

test('create returns an ok response', function (): void {
    $response = $this->actingAs($this->staffUser)->get(route('staff.articles.create'));
    $response->assertOk();
    $response->assertViewIs('Staff.article.create');
});

test('destroy returns an ok response', function (): void {
    $article = Article::factory()->create();

    $response = $this->actingAs($this->staffUser)->delete(route('staff.articles.destroy', [$article]));
    $response->assertRedirect(route('staff.articles.index'));
    $response->assertSessionHas('success', trans('application-messages.flash.article-deleted'));

    $this->assertModelMissing($article);
});

test('edit returns an ok response', function (): void {
    $article = Article::factory()->create();

    $response = $this->actingAs($this->staffUser)->get(route('staff.articles.edit', [$article]));
    $response->assertOk();
    $response->assertViewIs('Staff.article.edit');
    $response->assertViewHas('article', $article);
});

test('index returns an ok response', function (): void {
    $response = $this->actingAs($this->staffUser)->get(route('staff.articles.index'));
    $response->assertOk();
    $response->assertViewIs('Staff.article.index');
    $response->assertViewHas('articles');
});

test('store validates with a form request', function (): void {
    $this->assertActionUsesFormRequest(
        ArticleController::class,
        'store',
        StoreArticleRequest::class
    );
});

test('store returns an ok response', function (): void {
    $response = $this->actingAs($this->staffUser)->post(route('staff.articles.store'), [
        'title'   => 'Test Article',
        'content' => 'Test Content',
    ]);
    $response->assertRedirect(route('staff.articles.index'));
    $response->assertSessionHas('success', trans('application-messages.flash.article-published'));
});

test('update validates with a form request', function (): void {
    $this->assertActionUsesFormRequest(
        ArticleController::class,
        'update',
        UpdateArticleRequest::class
    );
});

test('update returns an ok response', function (): void {
    $article = Article::factory()->create();

    $response = $this->actingAs($this->staffUser)->post(route('staff.articles.update', [$article]), [
        'title'   => 'Test Article Updated',
        'content' => 'Test Content Updated',
    ]);
    $response->assertRedirect(route('staff.articles.index'));
    $response->assertSessionHas('success', trans('application-messages.flash.article-changes-published'));
});

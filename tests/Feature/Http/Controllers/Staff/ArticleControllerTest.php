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
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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

test('store returns an ok response', function (): void {
    $response = $this->actingAs($this->staffUser)->post(route('staff.articles.store'), [
        'title'   => 'Test Article',
        'content' => 'Test Content',
    ]);
    $response->assertRedirect(route('staff.articles.index'));
    $response->assertSessionHas('success', trans('application-messages.flash.article-published'));
});

test('article image uploads persist a readable thumbnail', function (string $action): void {
    Storage::fake('article-images');
    $article = $action === 'update' ? Article::factory()->create(['image' => 'previous.png']) : null;

    if ($article !== null) {
        Storage::disk('article-images')->put('previous.png', 'previous image');
    }

    $response = $this->actingAs($this->staffUser)->post(
        $action === 'update' ? route('staff.articles.update', [$article]) : route('staff.articles.store'),
        ['title'    => 'Image upload regression', 'content' => 'Article content',
            'image' => UploadedFile::fake()->image('photo.jpg', 160, 120)],
    );
    $response->assertRedirect(route('staff.articles.index'));
    $response->assertSessionHasNoErrors();
    $saved = $article?->fresh() ?? Article::query()->where('title', 'Image upload regression')->sole();
    $disk = Storage::disk('article-images');
    $disk->assertExists($saved->image);
    $info = getimagesizefromstring($disk->get($saved->image));
    expect([$info[0], $info[1], $info['mime']])->toBe([75, 75, 'image/png']);

    if ($article !== null) {
        $disk->assertMissing('previous.png');
    }
})->with(['store', 'update']);

test('editing article text keeps its existing image', function (): void {
    $article = Article::factory()->create(['image' => 'existing.png']);
    $this->actingAs($this->staffUser)->post(route('staff.articles.update', [$article]), [
        'title' => 'Changed title', 'content' => 'Changed content',
    ])->assertRedirect(route('staff.articles.index'));
    expect($article->fresh()->image)->toBe('existing.png');
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

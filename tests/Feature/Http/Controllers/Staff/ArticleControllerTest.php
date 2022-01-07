<?php

namespace Tests\Feature\Http\Controllers\Staff;

use App\Models\Article;
use App\Models\Group;
use App\Models\User;
use Database\Seeders\GroupsTableSeeder;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Staff\ArticleController
 */
class ArticleControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function createStaffUser()
    {
        return User::factory()->create([
            'group_id' => fn () => Group::factory()->create([
                'is_owner' => true,
                'is_admin' => true,
                'is_modo'  => true,
            ])->id,
        ]);
    }

    public function testCreateReturnsAnOkResponse()
    {
        $this->seed(GroupsTableSeeder::class);

        $user = $this->createStaffUser();

        $response = $this->actingAs($user)->get(route('staff.articles.create'));

        $response->assertOk();
        $response->assertViewIs('Staff.article.create');
    }

    public function testDestroyReturnsAnOkResponse()
    {
        $this->seed(GroupsTableSeeder::class);

        $user = $this->createStaffUser();
        $article = Article::factory()->create();

        $response = $this->actingAs($user)->delete(route('staff.articles.destroy', ['id' => $article->id]));
        $response->assertRedirect(route('staff.articles.index'));
    }

    public function testEditReturnsAnOkResponse()
    {
        $this->seed(GroupsTableSeeder::class);

        $user = $this->createStaffUser();
        $article = Article::factory()->create();

        $response = $this->actingAs($user)->get(route('staff.articles.edit', ['id' => $article->id]));

        $response->assertOk();
        $response->assertViewIs('Staff.article.edit');
        $response->assertViewHas('article');
    }

    public function testIndexReturnsAnOkResponse()
    {
        $this->seed(GroupsTableSeeder::class);

        $user = $this->createStaffUser();

        $response = $this->actingAs($user)->get(route('staff.articles.index'));

        $response->assertOk();
        $response->assertViewIs('Staff.article.index');
        $response->assertViewHas('articles');
    }

    public function testStoreReturnsAnOkResponse()
    {
        $this->seed(GroupsTableSeeder::class);

        $user = $this->createStaffUser();
        $article = Article::factory()->make();

        $response = $this->actingAs($user)->post(route('staff.articles.store'), [
            'title'   => $article->title,
            'slug'    => $article->slug,
            'content' => $article->content,
            'user_id' => $user->id,
        ]);

        $response->assertRedirect(route('staff.articles.index'));
    }

    public function testUpdateReturnsAnOkResponse()
    {
        $this->seed(GroupsTableSeeder::class);

        $user = $this->createStaffUser();
        $article = Article::factory()->create();

        $response = $this->actingAs($user)->post(route('staff.articles.update', ['id' => $article->id]), [
            'title'   => $article->title,
            'slug'    => $article->slug,
            'content' => $article->content,
            'user_id' => $user->id,
        ]);

        $response->assertRedirect(route('staff.articles.index'));
    }
}

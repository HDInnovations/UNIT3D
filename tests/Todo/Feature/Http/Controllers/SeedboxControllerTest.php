<?php

namespace Tests\Todo\Feature\Http\Controllers;

use App\Models\Seedbox;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\SeedboxController
 */
class SeedboxControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndexReturnsAnOkResponse()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $seedbox = Seedbox::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('seedboxes.index', ['username' => $seedbox->username]));

        $response->assertOk();
        $response->assertViewIs('seedbox.index');
        $response->assertViewHas('user');
        $response->assertViewHas('seedboxes');

        // TODO: perform additional assertions
    }

    // test cases...
}

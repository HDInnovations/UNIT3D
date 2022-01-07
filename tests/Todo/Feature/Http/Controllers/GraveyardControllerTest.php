<?php

namespace Tests\Todo\Feature\Http\Controllers;

use App\Models\Graveyard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\GraveyardController
 */
class GraveyardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testDestroyReturnsAnOkResponse()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $graveyard = Graveyard::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete(route('graveyard.destroy', ['id' => $graveyard->id]));

        $response->assertRedirect(withSuccess('Resurrection Successfully Canceled!'));
        $this->assertDeleted($graveyard);

        // TODO: perform additional assertions
    }

    public function testFacetedReturnsAnOkResponse()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('graveyard.'));

        $response->assertOk();
        $response->assertViewIs();

        // TODO: perform additional assertions
    }

    public function testIndexReturnsAnOkResponse()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('graveyard.index'));

        $response->assertOk();
        $response->assertViewIs('graveyard.index');
        $response->assertViewHas('user');
        $response->assertViewHas('torrents');
        $response->assertViewHas('repository');
        $response->assertViewHas('deadcount');

        // TODO: perform additional assertions
    }

    public function testStoreReturnsAnOkResponse()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $graveyard = Graveyard::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('graveyard.store', ['id' => $graveyard->id]), [
            // TODO: send request data
        ]);

        $response->assertRedirect(withErrors('Torrent Resurrection Failed! This torrent is already pending a resurrection.'));

        // TODO: perform additional assertions
    }

    // test cases...
}

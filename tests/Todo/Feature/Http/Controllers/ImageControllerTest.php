<?php

namespace Tests\Todo\Feature\Http\Controllers;

use App\Models\Image;
use App\Models\User;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ImageController
 */
class ImageControllerTest extends TestCase
{
    public function testCreateReturnsAnOkResponse()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $image = Image::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('images.create', ['id' => $image->id]));

        $response->assertOk();
        $response->assertViewIs('album.image');
        $response->assertViewHas('album');

        // TODO: perform additional assertions
    }

    public function testDestroyReturnsAnOkResponse()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $image = Image::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete(route('images.destroy', ['id' => $image->id]));

        $response->assertRedirect(withSuccess('Image has successfully been deleted'));
        $this->assertDeleted($images);

        // TODO: perform additional assertions
    }

    public function testDownloadReturnsAnOkResponse()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $image = Image::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('images.download', ['id' => $image->id]));

        $response->assertRedirect(withErrors('Image File Not Found! Please Report This To Staff!'));

        // TODO: perform additional assertions
    }

    public function testStoreReturnsAnOkResponse()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('images.store'), [
            // TODO: send request data
        ]);

        $response->assertRedirect(withErrors($v->errors()));

        // TODO: perform additional assertions
    }

    // test cases...
}

<?php

namespace Tests\Todo\Feature\Http\Controllers;

use App\Models\PlaylistTorrent;
use App\Models\User;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PlaylistTorrentController
 */
class PlaylistTorrentControllerTest extends TestCase
{
    public function testDestroyReturnsAnOkResponse()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $playlist_torrent = PlaylistTorrent::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete(route('playlists.detach', ['id' => $playlist_torrent->id]));

        $response->assertRedirect(withSuccess('Torrent Has Successfully Been Detached From Your Playlist.'));
        $this->assertDeleted($playlists);

        // TODO: perform additional assertions
    }

    public function testStoreReturnsAnOkResponse()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('playlists.attach'), [
            // TODO: send request data
        ]);

        $response->assertRedirect(withErrors($v->errors()));

        // TODO: perform additional assertions
    }

    // test cases...
}

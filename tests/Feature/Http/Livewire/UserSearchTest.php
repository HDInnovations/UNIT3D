<?php

declare(strict_types=1);

use App\Http\Livewire\UserSearch;
use App\Models\BlockedIp;
use App\Models\FailedLoginAttempt;
use App\Models\Note;
use App\Models\Seedbox;
use App\Models\Torrent;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

test('user search shows sitewide ip matches', function (): void {
    $peerUser = User::factory()->create([
        'username' => 'peeruser3658',
    ]);
    $sessionUser = User::factory()->create([
        'username' => 'sessionuser3658',
    ]);
    $failedLoginUser = User::factory()->create([
        'username' => 'failedloginuser3658',
    ]);
    $blockedIpUser = User::factory()->create([
        'username' => 'blockedipuser3658',
    ]);
    $seedboxUser = User::factory()->create([
        'username' => 'seedboxuser3658',
    ]);
    $notedUser = User::factory()->create([
        'username' => 'noteduser3658',
    ]);
    $staffUser = User::factory()->create([
        'username' => 'staffuser3658',
    ]);
    $torrent = Torrent::factory()->create();

    DB::table('peers')->insert([
        'peer_id'     => str_repeat('a', 20),
        'ip'          => inet_pton('203.0.113.10'),
        'port'        => 12345,
        'agent'       => 'qBittorrent',
        'uploaded'    => 0,
        'downloaded'  => 0,
        'left'        => 0,
        'seeder'      => true,
        'created_at'  => now(),
        'updated_at'  => now(),
        'torrent_id'  => $torrent->id,
        'user_id'     => $peerUser->id,
        'connectable' => true,
        'active'      => true,
        'visible'     => true,
    ]);

    DB::table('sessions')->insert([
        'id'            => 'sitewide-ip-search-session',
        'user_id'       => $sessionUser->id,
        'ip_address'    => '203.0.113.11',
        'user_agent'    => 'Mozilla test agent',
        'payload'       => 'payload',
        'last_activity' => now()->timestamp,
    ]);

    FailedLoginAttempt::factory()->create([
        'user_id'    => $failedLoginUser->id,
        'username'   => $failedLoginUser->username,
        'ip_address' => '203.0.113.12',
    ]);
    BlockedIp::factory()->create([
        'user_id'    => $blockedIpUser->id,
        'ip_address' => '203.0.113.13',
        'reason'     => 'Known proxy',
    ]);
    Seedbox::factory()->create([
        'user_id' => $seedboxUser->id,
        'name'    => 'Main seedbox',
        'ip'      => '203.0.113.14',
    ]);
    Note::factory()->create([
        'user_id'  => $notedUser->id,
        'staff_id' => $staffUser->id,
        'message'  => 'Reviewed login from 203.0.113.15',
    ]);

    Livewire::test(UserSearch::class)
        ->set('ipAddress', '203.0.113')
        ->assertSee('IP matches')
        ->assertSee('peeruser3658')
        ->assertSee('sessionuser3658')
        ->assertSee('failedloginuser3658')
        ->assertSee('blockedipuser3658')
        ->assertSee('seedboxuser3658')
        ->assertSee('noteduser3658')
        ->assertSee('203.0.113.10')
        ->assertSee('203.0.113.11')
        ->assertSee('203.0.113.12')
        ->assertSee('203.0.113.13')
        ->assertSee('203.0.113.14')
        ->assertSee('203.0.113.15');
});

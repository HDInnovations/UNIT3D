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
 * @author     Obi-Wana
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

use App\Models\Collectible;
use App\Models\CollectibleCategory;
use App\Models\CollectibleItem;
use App\Models\CollectibleRequirement;
use App\Models\CollectibleTransaction;
use App\Models\History;
use App\Models\Torrent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('show collectibles with categories returns an ok response', function (): void {
    $user = User::factory()->create();

    $collectibleCategory1 = CollectibleCategory::factory()->create();
    $collectibleCategory2 = CollectibleCategory::factory()->create();
    $collectible1 = Collectible::factory()->create([
        'category_id' => $collectibleCategory1->id,
    ]);
    $collectible2 = Collectible::factory()->create([
        'category_id' => $collectibleCategory2->id,
    ]);

    $response = $this->actingAs($user)->get(route('collectibles.index'));

    $response->assertOk();
    $response->assertViewIs('collectible.index');

    $response->assertViewHas('collectibles');

    $response->assertSee($collectibleCategory1->name);
    $response->assertSee($collectibleCategory2->name);
    $response->assertSee($collectible1->name);
    $response->assertSee($collectible2->name);
});

test('show available collectible item returns an ok response', function (): void {
    $torrent = Torrent::factory()->create([
        'size' => 1024,
    ]);

    $user = User::factory()->create([
        'uploaded'   => 1024,
        'downloaded' => 1023,
        'created_at' => now()->subDays(10),
    ]);
    $userHistory = History::factory()->create([
        'user_id'    => $user->id,
        'torrent_id' => $torrent->id,
        'seedtime'   => 60,
        'active'     => 1,
        'seeder'     => 1,
    ]);

    $collectible = Collectible::factory()->create();

    $collectibleRequirement = CollectibleRequirement::factory()->create([
        'collectible_id'   => $collectible->id,
        'min_uploaded'     => $user->uploaded - 10,
        'min_seedsize'     => $torrent->size - 10,
        'min_avg_seedtime' => $userHistory->seedtime - 10,
        'min_ratio'        => 0,
        'min_age'          => 60,
    ]);

    // Create unassigned items to simulate store availability
    $collectibleItem = CollectibleItem::factory()->count(2)->create([
        'collectible_id' => $collectible->id,
        'user_id'        => null,
    ]);

    $response = $this->actingAs($user)->get(route('collectibles.show', $collectible->id));

    $response->assertOk();
    $response->assertViewIs('collectible.show');

    $response->assertViewHas('collectible', fn ($c) => $c->id === $collectible->id);
    $response->assertViewHas('user', fn ($u) => $u->id === $user->id);
    $response->assertViewHas('userOwns');
    $response->assertViewHas('userTransaction');
    $response->assertViewHas('requirements');
    $response->assertViewHas('userMeetsAllRequirements');

    $response->assertSee('Buy from Store');
});

test('show available collectible item with min_uploaded requirement unfilled returns an ok response', function (): void {
    $torrent = Torrent::factory()->create([
        'size' => 1024,
    ]);

    $user = User::factory()->create([
        'uploaded'   => 1024,
        'downloaded' => 1023,
        'created_at' => now()->subDays(10),
    ]);
    $userHistory = History::factory()->create([
        'user_id'    => $user->id,
        'torrent_id' => $torrent->id,
        'seedtime'   => 60,
        'active'     => 1,
        'seeder'     => 1,
    ]);

    $collectible = Collectible::factory()->create();

    $collectibleRequirement = CollectibleRequirement::factory()->create([
        'collectible_id'   => $collectible->id,
        'min_uploaded'     => $user->uploaded + 10,
        'min_seedsize'     => $torrent->size - 10,
        'min_avg_seedtime' => $userHistory->seedtime - 10,
        'min_ratio'        => 0,
        'min_age'          => 60,
    ]);

    // Create unassigned items to simulate store availability
    $collectibleItem = CollectibleItem::factory()->count(2)->create([
        'collectible_id' => $collectible->id,
        'user_id'        => null,
    ]);

    $response = $this->actingAs($user)->get(route('collectibles.show', $collectible->id));

    $response->assertOk();
    $response->assertViewIs('collectible.show');

    $response->assertViewHas('collectible', fn ($c) => $c->id === $collectible->id);
    $response->assertViewHas('user', fn ($u) => $u->id === $user->id);
    $response->assertViewHas('userOwns');
    $response->assertViewHas('userTransaction');
    $response->assertViewHas('requirements');
    $response->assertViewHas('userMeetsAllRequirements');

    $response->assertDontSeeText('Buy from Store');
});

test('show available collectible item with min_seedsize requirement unfilled returns an ok response', function (): void {
    $torrent = Torrent::factory()->create([
        'size' => 1024,
    ]);

    $user = User::factory()->create([
        'uploaded'   => 1024,
        'downloaded' => 1023,
        'created_at' => now()->subDays(10),
    ]);
    $userHistory = History::factory()->create([
        'user_id'    => $user->id,
        'torrent_id' => $torrent->id,
        'seedtime'   => 60,
        'active'     => 1,
        'seeder'     => 1,
    ]);

    $collectible = Collectible::factory()->create();

    $collectibleRequirement = CollectibleRequirement::factory()->create([
        'collectible_id'   => $collectible->id,
        'min_uploaded'     => $user->uploaded - 10,
        'min_seedsize'     => $torrent->size + 10,
        'min_avg_seedtime' => $userHistory->seedtime - 10,
        'min_ratio'        => 0,
        'min_age'          => 60,
    ]);

    // Create unassigned items to simulate store availability
    $collectibleItem = CollectibleItem::factory()->count(2)->create([
        'collectible_id' => $collectible->id,
        'user_id'        => null,
    ]);

    $response = $this->actingAs($user)->get(route('collectibles.show', $collectible->id));

    $response->assertOk();
    $response->assertViewIs('collectible.show');

    $response->assertViewHas('collectible', fn ($c) => $c->id === $collectible->id);
    $response->assertViewHas('user', fn ($u) => $u->id === $user->id);
    $response->assertViewHas('userOwns');
    $response->assertViewHas('userTransaction');
    $response->assertViewHas('requirements');
    $response->assertViewHas('userMeetsAllRequirements');

    $response->assertDontSeeText('Buy from Store');
});

test('show available collectible item with min_avg_seedtime requirement unfilled returns an ok response', function (): void {
    $torrent = Torrent::factory()->create([
        'size' => 1024,
    ]);

    $user = User::factory()->create([
        'uploaded'   => 1024,
        'downloaded' => 1023,
        'created_at' => now()->subDays(10),
    ]);
    $userHistory = History::factory()->create([
        'user_id'    => $user->id,
        'torrent_id' => $torrent->id,
        'seedtime'   => 60,
        'active'     => 1,
        'seeder'     => 1,
    ]);

    $collectible = Collectible::factory()->create();

    $collectibleRequirement = CollectibleRequirement::factory()->create([
        'collectible_id'   => $collectible->id,
        'min_uploaded'     => $user->uploaded - 10,
        'min_seedsize'     => $torrent->size - 10,
        'min_avg_seedtime' => $userHistory->seedtime + 10,
        'min_ratio'        => 0,
        'min_age'          => 60,
    ]);

    // Create unassigned items to simulate store availability
    $collectibleItem = CollectibleItem::factory()->count(2)->create([
        'collectible_id' => $collectible->id,
        'user_id'        => null,
    ]);

    $response = $this->actingAs($user)->get(route('collectibles.show', $collectible->id));

    $response->assertOk();
    $response->assertViewIs('collectible.show');

    $response->assertViewHas('collectible', fn ($c) => $c->id === $collectible->id);
    $response->assertViewHas('user', fn ($u) => $u->id === $user->id);
    $response->assertViewHas('userOwns');
    $response->assertViewHas('userTransaction');
    $response->assertViewHas('requirements');
    $response->assertViewHas('userMeetsAllRequirements');

    $response->assertDontSeeText('Buy from Store');
});

test('show available collectible item with min_ratio requirement unfilled returns an ok response', function (): void {
    $torrent = Torrent::factory()->create([
        'size' => 1024,
    ]);

    $user = User::factory()->create([
        'uploaded'   => 1024,
        'downloaded' => 1023,
        'created_at' => now()->subDays(10),
    ]);
    $userHistory = History::factory()->create([
        'user_id'    => $user->id,
        'torrent_id' => $torrent->id,
        'seedtime'   => 60,
        'active'     => 1,
        'seeder'     => 1,
    ]);

    $collectible = Collectible::factory()->create();

    $collectibleRequirement = CollectibleRequirement::factory()->create([
        'collectible_id'   => $collectible->id,
        'min_uploaded'     => $user->uploaded - 10,
        'min_seedsize'     => $torrent->size - 10,
        'min_avg_seedtime' => $userHistory->seedtime - 10,
        'min_ratio'        => 10,
        'min_age'          => 60,
    ]);

    // Create unassigned items to simulate store availability
    $collectibleItem = CollectibleItem::factory()->count(2)->create([
        'collectible_id' => $collectible->id,
        'user_id'        => null,
    ]);

    $response = $this->actingAs($user)->get(route('collectibles.show', $collectible->id));

    $response->assertOk();
    $response->assertViewIs('collectible.show');

    $response->assertViewHas('collectible', fn ($c) => $c->id === $collectible->id);
    $response->assertViewHas('user', fn ($u) => $u->id === $user->id);
    $response->assertViewHas('userOwns');
    $response->assertViewHas('userTransaction');
    $response->assertViewHas('requirements');
    $response->assertViewHas('userMeetsAllRequirements');

    $response->assertDontSeeText('Buy from Store');
});

test('show available collectible item with min_age requirement unfilled returns an ok response', function (): void {
    $torrent = Torrent::factory()->create([
        'size' => 1024,
    ]);

    $user = User::factory()->create([
        'uploaded'   => 1024,
        'downloaded' => 1023,
        'created_at' => now(),
    ]);
    $userHistory = History::factory()->create([
        'user_id'    => $user->id,
        'torrent_id' => $torrent->id,
        'seedtime'   => 60,
        'active'     => 1,
        'seeder'     => 1,
    ]);

    $collectible = Collectible::factory()->create();

    $collectibleRequirement = CollectibleRequirement::factory()->create([
        'collectible_id'   => $collectible->id,
        'min_uploaded'     => $user->uploaded - 10,
        'min_seedsize'     => $torrent->size - 10,
        'min_avg_seedtime' => $userHistory->seedtime - 10,
        'min_ratio'        => 0,
        'min_age'          => 60 * 24,
    ]);

    // Create unassigned items to simulate store availability
    $collectibleItem = CollectibleItem::factory()->count(2)->create([
        'collectible_id' => $collectible->id,
        'user_id'        => null,
    ]);

    $response = $this->actingAs($user)->get(route('collectibles.show', $collectible->id));

    $response->assertOk();
    $response->assertViewIs('collectible.show');

    $response->assertViewHas('collectible', fn ($c) => $c->id === $collectible->id);
    $response->assertViewHas('user', fn ($u) => $u->id === $user->id);
    $response->assertViewHas('userOwns');
    $response->assertViewHas('userTransaction');
    $response->assertViewHas('requirements');
    $response->assertViewHas('userMeetsAllRequirements');

    $response->assertDontSeeText('Buy from Store');
});

test('show available collectible item with all requirement unfilled returns an ok response', function (): void {
    $torrent = Torrent::factory()->create([
        'size' => 1024,
    ]);

    $user = User::factory()->create([
        'uploaded'   => 1024,
        'downloaded' => 1023,
        'created_at' => now(),
    ]);
    $userHistory = History::factory()->create([
        'user_id'    => $user->id,
        'torrent_id' => $torrent->id,
        'seedtime'   => 60,
        'active'     => 1,
        'seeder'     => 1,
    ]);

    $collectible = Collectible::factory()->create();

    $collectibleRequirement = CollectibleRequirement::factory()->create([
        'collectible_id'   => $collectible->id,
        'min_uploaded'     => $user->uploaded + 10,
        'min_seedsize'     => $torrent->size + 10,
        'min_avg_seedtime' => $userHistory->seedtime + 10,
        'min_ratio'        => 10,
        'min_age'          => 60 * 24,
    ]);

    // Create unassigned items to simulate store availability
    $collectibleItem = CollectibleItem::factory()->count(2)->create([
        'collectible_id' => $collectible->id,
        'user_id'        => null,
    ]);

    $response = $this->actingAs($user)->get(route('collectibles.show', $collectible->id));

    $response->assertOk();
    $response->assertViewIs('collectible.show');

    $response->assertViewHas('collectible', fn ($c) => $c->id === $collectible->id);
    $response->assertViewHas('user', fn ($u) => $u->id === $user->id);
    $response->assertViewHas('userOwns');
    $response->assertViewHas('userTransaction');
    $response->assertViewHas('requirements');
    $response->assertViewHas('userMeetsAllRequirements');

    $response->assertDontSeeText('Buy from Store');
});

test('show owned collectible returns an ok response', function (): void {
    $user = User::factory()->create();

    $collectible = Collectible::factory()->create();

    $requirements = CollectibleRequirement::factory()->count(3)->create([
        'collectible_id' => $collectible->id,
    ]);

    // Create unassigned items to simulate store availability
    $collectibleItem = CollectibleItem::factory()->create([
        'collectible_id' => $collectible->id,
        'user_id'        => $user->id,
    ]);
    $collectibleTransaction = CollectibleTransaction::factory()->create([
        'collectible_id' => $collectible->id,
        'buyer_id'       => $user->id,
    ]);

    $response = $this->actingAs($user)->get(route('collectibles.show', $collectible->id));

    $response->assertOk();
    $response->assertViewIs('collectible.show');

    $response->assertViewHas('collectible', fn ($c) => $c->id === $collectible->id);
    $response->assertViewHas('user', fn ($u) => $u->id === $user->id);
    $response->assertViewHas('userOwns');
    $response->assertViewHas('userTransaction');
    $response->assertViewHas('requirements');
    $response->assertViewHas('userMeetsAllRequirements');

    $response->assertSee('Sell');
});

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
use App\Models\CollectibleItem;
use App\Models\CollectibleRequirement;
use App\Models\History;
use App\Models\Torrent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('buy a collectible item from store returns an ok response', function (): void {
    $systemUser = User::factory()->create([
        'id' => 1,
    ]);

    $torrent = Torrent::factory()->create([
        'size' => 1024,
    ]);

    $buyer = User::factory()->create([
        'seedbonus'  => 1150,
        'uploaded'   => 1024,
        'downloaded' => 1023,
        'created_at' => now()->subDays(10),
    ]);
    $buyerHistory = History::factory()->create([
        'user_id'    => $buyer->id,
        'torrent_id' => $torrent->id,
        'seedtime'   => 60,
        'active'     => 1,
        'seeder'     => 1,
    ]);

    $collectible = Collectible::factory()->create([
        'price' => 1000,
    ]);

    $collectibleItem = CollectibleItem::factory()->create([
        'collectible_id' => $collectible->id,
        'user_id'        => null,
    ]);

    $collectibleRequirement = CollectibleRequirement::factory()->create([
        'collectible_id'   => $collectible->id,
        'min_uploaded'     => $buyer->uploaded - 10,
        'min_seedsize'     => $torrent->size - 10,
        'min_avg_seedtime' => $buyerHistory->seedtime - 10,
        'min_ratio'        => 0,
        'min_age'          => 60,
    ]);

    $response = $this->actingAs($buyer)->post(route('collectibles.transaction.create', ['collectible' => $collectible]));

    $response->assertRedirect(route('collectibles.show', ['collectible' => $collectible]));
    $response->assertSessionHas('success', 'Collectible bought.');

    $this->assertDatabaseHas('collectible_transactions', [
        'collectible_id' => $collectible->id,
        'seller_id'      => User::SYSTEM_USER_ID,
        'buyer_id'       => $buyer->id,
        'price'          => $collectible->price,
    ]);

    $this->assertDatabaseHas('collectible_items', [
        'collectible_id' => $collectible->id,
        'user_id'        => $buyer->id,
    ]);

    $this->assertDatabaseMissing('collectible_items', [
        'collectible_id' => $collectible->id,
        'user_id'        => null,
    ]);

    // Check seedbonus updated
    $buyer->refresh();
    expect($buyer->seedbonus)->toEqual(150);
});

test('buy a collectible item from store with insufficient bon returns an error response', function (): void {
    $buyer = User::factory()->create([
        'seedbonus' => 50,
    ]);

    $collectible = Collectible::factory()->create([
        'price' => 1000,
    ]);

    $collectibleItem = CollectibleItem::factory()->create([
        'collectible_id' => $collectible->id,
        'user_id'        => null,
    ]);

    $collectibleRequirement = CollectibleRequirement::factory()->create([
        'collectible_id'   => $collectible->id,
        'min_uploaded'     => null,
        'min_seedsize'     => null,
        'min_avg_seedtime' => null,
        'min_ratio'        => null,
        'min_age'          => null,
    ]);

    $response = $this->actingAs($buyer)->post(route('collectibles.transaction.create', ['collectible' => $collectible]));

    $response->assertRedirect(route('collectibles.show', ['collectible' => $collectible]));
    $response->assertSessionHas('errors');
    $this->assertContains('Not enough BON.', session('errors')->all());

    $this->assertDatabaseMissing('collectible_transactions', [
        'collectible_id' => $collectible->id,
        'seller_id'      => User::SYSTEM_USER_ID,
        'buyer_id'       => $buyer->id,
        'price'          => $collectible->price,
    ]);

    $this->assertDatabaseMissing('collectible_items', [
        'collectible_id' => $collectible->id,
        'user_id'        => $buyer->id,
    ]);

    $this->assertDatabaseHas('collectible_items', [
        'collectible_id' => $collectible->id,
        'user_id'        => null,
    ]);
});

test('buy already owned collectible item from store returns an error response', function (): void {
    $buyer = User::factory()->create([
        'seedbonus' => 50,
    ]);

    $collectible = Collectible::factory()->create([
        'price' => 1000,
    ]);

    $collectibleItem = CollectibleItem::factory()->create([
        'collectible_id' => $collectible->id,
        'user_id'        => $buyer->id,
    ]);

    $collectibleRequirement = CollectibleRequirement::factory()->create([
        'collectible_id'   => $collectible->id,
        'min_uploaded'     => null,
        'min_seedsize'     => null,
        'min_avg_seedtime' => null,
        'min_ratio'        => null,
        'min_age'          => null,
    ]);

    $response = $this->actingAs($buyer)->post(route('collectibles.transaction.create', ['collectible' => $collectible]));

    $response->assertRedirect(route('collectibles.show', ['collectible' => $collectible]));
    $response->assertSessionHas('errors');
    $this->assertContains('You already own this item.', session('errors')->all());

    $this->assertDatabaseMissing('collectible_transactions', [
        'collectible_id' => $collectible->id,
        'seller_id'      => User::SYSTEM_USER_ID,
        'buyer_id'       => $buyer->id,
        'price'          => $collectible->price,
    ]);
});

test('buy collectible item from store with min_uploaded requirement unfilled returns an error response', function (): void {
    $torrent = Torrent::factory()->create([
        'size' => 1024,
    ]);

    $buyer = User::factory()->create([
        'seedbonus'  => 1150,
        'uploaded'   => 1024,
        'downloaded' => 1023,
        'created_at' => now()->subDays(10),
    ]);
    $buyerHistory = History::factory()->create([
        'user_id'    => $buyer->id,
        'torrent_id' => $torrent->id,
        'seedtime'   => 60,
        'active'     => 1,
        'seeder'     => 1,
    ]);

    $collectible = Collectible::factory()->create([
        'price' => 1000,
    ]);

    $collectibleItem = CollectibleItem::factory()->create([
        'collectible_id' => $collectible->id,
        'user_id'        => null,
    ]);

    $collectibleRequirement = CollectibleRequirement::factory()->create([
        'collectible_id'   => $collectible->id,
        'min_uploaded'     => $buyer->uploaded + 10,
        'min_seedsize'     => $torrent->size - 10,
        'min_avg_seedtime' => $buyerHistory->seedtime - 10,
        'min_ratio'        => 0,
        'min_age'          => 60,
    ]);

    $response = $this->actingAs($buyer)->post(route('collectibles.transaction.create', ['collectible' => $collectible]));

    $response->assertRedirect(route('collectibles.show', ['collectible' => $collectible]));
    $response->assertSessionHas('errors');
    $this->assertContains('You do not meet all requirements to buy this item!', session('errors')->all());

    $this->assertDatabaseMissing('collectible_transactions', [
        'collectible_id' => $collectible->id,
        'seller_id'      => User::SYSTEM_USER_ID,
        'buyer_id'       => $buyer->id,
        'price'          => $collectible->price,
    ]);
});

test('buy collectible item from store with min_seedsize requirement unfilled returns an error response', function (): void {
    $torrent = Torrent::factory()->create([
        'size' => 1024,
    ]);

    $buyer = User::factory()->create([
        'seedbonus'  => 1150,
        'uploaded'   => 1024,
        'downloaded' => 1023,
        'created_at' => now()->subDays(10),
    ]);
    $buyerHistory = History::factory()->create([
        'user_id'    => $buyer->id,
        'torrent_id' => $torrent->id,
        'seedtime'   => 60,
        'active'     => 1,
        'seeder'     => 1,
    ]);

    $collectible = Collectible::factory()->create([
        'price' => 1000,
    ]);

    $collectibleItem = CollectibleItem::factory()->create([
        'collectible_id' => $collectible->id,
        'user_id'        => null,
    ]);

    $collectibleRequirement = CollectibleRequirement::factory()->create([
        'collectible_id'   => $collectible->id,
        'min_uploaded'     => $buyer->uploaded - 10,
        'min_seedsize'     => $torrent->size + 10,
        'min_avg_seedtime' => $buyerHistory->seedtime - 10,
        'min_ratio'        => 0,
        'min_age'          => 60,
    ]);

    $response = $this->actingAs($buyer)->post(route('collectibles.transaction.create', ['collectible' => $collectible]));

    $response->assertRedirect(route('collectibles.show', ['collectible' => $collectible]));
    $response->assertSessionHas('errors');
    $this->assertContains('You do not meet all requirements to buy this item!', session('errors')->all());

    $this->assertDatabaseMissing('collectible_transactions', [
        'collectible_id' => $collectible->id,
        'seller_id'      => User::SYSTEM_USER_ID,
        'buyer_id'       => $buyer->id,
        'price'          => $collectible->price,
    ]);
});

test('buy collectible item from store with min_avg_seedtime requirement unfilled returns an error response', function (): void {
    $torrent = Torrent::factory()->create([
        'size' => 1024,
    ]);

    $buyer = User::factory()->create([
        'seedbonus'  => 1150,
        'uploaded'   => 1024,
        'downloaded' => 1023,
        'created_at' => now()->subDays(10),
    ]);
    $buyerHistory = History::factory()->create([
        'user_id'    => $buyer->id,
        'torrent_id' => $torrent->id,
        'seedtime'   => 60,
        'active'     => 1,
        'seeder'     => 1,
    ]);

    $collectible = Collectible::factory()->create([
        'price' => 1000,
    ]);

    $collectibleItem = CollectibleItem::factory()->create([
        'collectible_id' => $collectible->id,
        'user_id'        => null,
    ]);

    $collectibleRequirement = CollectibleRequirement::factory()->create([
        'collectible_id'   => $collectible->id,
        'min_uploaded'     => $buyer->uploaded - 10,
        'min_seedsize'     => $torrent->size - 10,
        'min_avg_seedtime' => $buyerHistory->seedtime + 10,
        'min_ratio'        => 0,
        'min_age'          => 60,
    ]);

    $response = $this->actingAs($buyer)->post(route('collectibles.transaction.create', ['collectible' => $collectible]));

    $response->assertRedirect(route('collectibles.show', ['collectible' => $collectible]));
    $response->assertSessionHas('errors');
    $this->assertContains('You do not meet all requirements to buy this item!', session('errors')->all());

    $this->assertDatabaseMissing('collectible_transactions', [
        'collectible_id' => $collectible->id,
        'seller_id'      => User::SYSTEM_USER_ID,
        'buyer_id'       => $buyer->id,
        'price'          => $collectible->price,
    ]);
});

test('buy collectible item from store with min_ratio requirement unfilled returns an error response', function (): void {
    $torrent = Torrent::factory()->create([
        'size' => 1024,
    ]);

    $buyer = User::factory()->create([
        'seedbonus'  => 1150,
        'uploaded'   => 1024,
        'downloaded' => 1023,
        'created_at' => now()->subDays(10),
    ]);
    $buyerHistory = History::factory()->create([
        'user_id'    => $buyer->id,
        'torrent_id' => $torrent->id,
        'seedtime'   => 60,
        'active'     => 1,
        'seeder'     => 1,
    ]);

    $collectible = Collectible::factory()->create([
        'price' => 1000,
    ]);

    $collectibleItem = CollectibleItem::factory()->create([
        'collectible_id' => $collectible->id,
        'user_id'        => null,
    ]);

    $collectibleRequirement = CollectibleRequirement::factory()->create([
        'collectible_id'   => $collectible->id,
        'min_uploaded'     => $buyer->uploaded - 10,
        'min_seedsize'     => $torrent->size - 10,
        'min_avg_seedtime' => $buyerHistory->seedtime - 10,
        'min_ratio'        => 10,
        'min_age'          => 60,
    ]);

    $response = $this->actingAs($buyer)->post(route('collectibles.transaction.create', ['collectible' => $collectible]));

    $response->assertRedirect(route('collectibles.show', ['collectible' => $collectible]));
    $response->assertSessionHas('errors');
    $this->assertContains('You do not meet all requirements to buy this item!', session('errors')->all());

    $this->assertDatabaseMissing('collectible_transactions', [
        'collectible_id' => $collectible->id,
        'seller_id'      => User::SYSTEM_USER_ID,
        'buyer_id'       => $buyer->id,
        'price'          => $collectible->price,
    ]);
});

test('buy collectible item from store with min_age requirement unfilled returns an error response', function (): void {
    $torrent = Torrent::factory()->create([
        'size' => 1024,
    ]);

    $buyer = User::factory()->create([
        'seedbonus'  => 1150,
        'uploaded'   => 1024,
        'downloaded' => 1023,
        'created_at' => now(),
    ]);
    $buyerHistory = History::factory()->create([
        'user_id'    => $buyer->id,
        'torrent_id' => $torrent->id,
        'seedtime'   => 60,
        'active'     => 1,
        'seeder'     => 1,
    ]);

    $collectible = Collectible::factory()->create([
        'price' => 1000,
    ]);

    $collectibleItem = CollectibleItem::factory()->create([
        'collectible_id' => $collectible->id,
        'user_id'        => null,
    ]);

    $collectibleRequirement = CollectibleRequirement::factory()->create([
        'collectible_id'   => $collectible->id,
        'min_uploaded'     => $buyer->uploaded - 10,
        'min_seedsize'     => $torrent->size - 10,
        'min_avg_seedtime' => $buyerHistory->seedtime - 10,
        'min_ratio'        => 0,
        'min_age'          => 60 * 24,
    ]);

    $response = $this->actingAs($buyer)->post(route('collectibles.transaction.create', ['collectible' => $collectible]));

    $response->assertRedirect(route('collectibles.show', ['collectible' => $collectible]));
    $response->assertSessionHas('errors');
    $this->assertContains('You do not meet all requirements to buy this item!', session('errors')->all());

    $this->assertDatabaseMissing('collectible_transactions', [
        'collectible_id' => $collectible->id,
        'seller_id'      => User::SYSTEM_USER_ID,
        'buyer_id'       => $buyer->id,
        'price'          => $collectible->price,
    ]);
});

test('buy collectible item from store with all requirement unfilled returns an error response', function (): void {
    $torrent = Torrent::factory()->create([
        'size' => 1024,
    ]);

    $buyer = User::factory()->create([
        'seedbonus'  => 1150,
        'uploaded'   => 1024,
        'downloaded' => 1023,
        'created_at' => now(),
    ]);
    $buyerHistory = History::factory()->create([
        'user_id'    => $buyer->id,
        'torrent_id' => $torrent->id,
        'seedtime'   => 60,
        'active'     => 1,
        'seeder'     => 1,
    ]);

    $collectible = Collectible::factory()->create([
        'price' => 1000,
    ]);

    $collectibleItem = CollectibleItem::factory()->create([
        'collectible_id' => $collectible->id,
        'user_id'        => null,
    ]);

    $collectibleRequirement = CollectibleRequirement::factory()->create([
        'collectible_id'   => $collectible->id,
        'min_uploaded'     => $buyer->uploaded + 10,
        'min_seedsize'     => $torrent->size + 10,
        'min_avg_seedtime' => $buyerHistory->seedtime + 10,
        'min_ratio'        => 10,
        'min_age'          => 60 * 24,
    ]);

    $response = $this->actingAs($buyer)->post(route('collectibles.transaction.create', ['collectible' => $collectible]));

    $response->assertRedirect(route('collectibles.show', ['collectible' => $collectible]));
    $response->assertSessionHas('errors');
    $this->assertContains('You do not meet all requirements to buy this item!', session('errors')->all());

    $this->assertDatabaseMissing('collectible_transactions', [
        'collectible_id' => $collectible->id,
        'seller_id'      => User::SYSTEM_USER_ID,
        'buyer_id'       => $buyer->id,
        'price'          => $collectible->price,
    ]);
});

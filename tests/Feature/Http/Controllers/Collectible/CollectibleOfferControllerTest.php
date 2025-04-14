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
use App\Models\CollectibleOffer;
use App\Models\CollectibleItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('view create collectible offer form returns an ok response ', function (): void {
    $user = User::factory()->create();
    $collectible = Collectible::factory()->create();

    $collectibleItem = CollectibleItem::factory()->create([
        'collectible_id' => $collectible->id,
        'user_id'        => $user->id,
    ]);

    $response = $this->actingAs($user)->get(route('collectible.offer.create', $collectible));
    $response->assertStatus(200);
});

test('create a new collectible offer returns an ok response', function (): void {
    $user = User::factory()->create([
        'seedbonus' => 2000,
    ]);
    $seller = User::factory()->create([
        'seedbonus' => 0,
    ]);

    $collectible = Collectible::factory()->create([
        'price' => 1000,
    ]);
    $collectibleItem = CollectibleItem::factory()->create([
        'collectible_id' => $collectible->id,
        'user_id'        => $seller->id,
    ]);

    $response = $this->actingAs($user)->post(route('collectible.offer.store', $collectible), [
        'sell_price' => 1100,
    ]);

    $response->assertRedirect(route('collectibles.show', ['collectible' => $collectible]));
    $response->assertSessionHas('success', 'Offer created.');

    $this->assertDatabaseHas('collectible_offers', [
        'collectible_id' => $collectible->id,
        'user_id'        => $user->id,
        'price'          => 1000,
    ]);
});

test('accept an offer returns an ok response', function (): void {
    $buyer = User::factory()->create([
        'seedbonus' => 1150,
    ]);
    $seller = User::factory()->create([
        'seedbonus' => 0,
    ]);

    $collectible = Collectible::factory()->create([
        'price' => 1000,
    ]);
    $collectibleItem = CollectibleItem::factory()->create([
        'collectible_id' => $collectible->id,
        'user_id'        => $seller->id,
    ]);

    $collectibleOffer = CollectibleOffer::factory()->create([
        'collectible_id' => $collectible->id,
        'user_id'        => $seller->id,
        'price'          => 1100,
        'filled_at'      => null,
    ]);

    $response = $this->actingAs($user)->post(route('collectible.offer.update', $offer));

    $response->assertRedirect(route('collectibles.show', ['collectible' => $collectible]));
    $response->assertSessionHas('success', 'Offer accepted.');

    $this->assertDatabaseHas('collectible_transactions', [
        'collectible_id' => $collectible->id,
        'seller_id'      => $seller->id,
        'buyer_id'       => $buyer->id,
        'price'          => 1100,
    ]);

    $this->assertDatabaseHas('collectible_offers', [
        'id'        => $offer->id,
        'filled_at' => notNull(),
    ]);

    // Check seedbonus updated
    $seller->refresh();
    $buyer->refresh();
    expect($seller->seedbonus)->toBe(1100);
    expect($buyer->seedbonus)->toBe(50);
});

test('accept own offer returns an error response', function (): void {
    $user = User::factory()->create(['
        seedbonus' => 1000,
    ]);

    $collectible = Collectible::factory()->create();
    $collectibleItem = CollectibleItem::factory()->create([
        'collectible_id' => $collectible->id,
        'user_id'        => $user->id,
    ]);

    $collectibleOffer = CollectibleOffer::factory()->create([
        'collectible_id' => $collectible->id,
        'user_id'        => $user->id,
        'price'          => 1100,
        'filled_at'      => null,
    ]);

    $response = $this->actingAs($user)->post(route('collectible.offer.update', $offer));

    $response->assertRedirect(route('collectibles.show', ['collectible' => $collectible]));
    $response->assertSessionHasErrors('You can not accept your own offer.');
});

test('accept offer of already owned item returns an error response', function (): void {
    $user = User::factory()->create();
    $randomUser = User::factory()->create();

    $collectible = Collectible::factory()->create();
    $item = CollectibleItem::factory()->create([
        'collectible_id' => $collectible->id,
        'user_id'        => $user->id,
    ]);

    $collectibleOffer = CollectibleOffer::factory()->create([
        'collectible_id' => $collectible->id,
        'user_id'        => $randomUser->id,
        'price'          => 1100,
        'filled_at'      => null,
    ]);

    $response = $this->actingAs($user)->post(route('collectible.offer.update', $offer));

    $response->assertRedirect(route('collectibles.show', ['collectible' => $collectible]));
    $response->assertSessionHasErrors('You already own this item.');
});

test('seller deletes their own offer returns an ok response', function (): void {
    $user = User::factory()->create();
    $seller = User::factory()->create();
    $collectible = Collectible::factory()->create();
    $offer = CollectibleOffer::factory()->create([
        'collectible_id' => $collectible->id,
        'user_id'        => $user->id,
        'seller_id'      => $seller->id,
    ]);

    $response = $this->actingAs($user)->delete(route('collectible.offer.destroy', $offer));

    $response->assertRedirect(route('collectibles.show', ['collectible' => $collectible]));
    $response->assertSessionHas('success', 'Offer deleted.');

    $this->assertDatabaseMissing('collectible_offers', ['id' => $offer->id]);
});

test('only seller or modo can delete offers returns an ok response', function (): void {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $moderator = User::factory()->create([
        'group' => ['is_modo' => true]
    ]);
    $seller = User::factory()->create();

    $collectible = Collectible::factory()->create();
    $collectibleOffer = CollectibleOffer::factory()->create([
        'collectible_id' => $collectible->id,
        'user_id'        => $seller->id,
    ]);

    $response = $this->actingAs($user2)->delete(route('collectible.offer.destroy', $offer));
    $response->assertStatus(403);

    $response = $this->actingAs($moderator)->delete(route('collectible.offer.destroy', $offer));
    $response->assertRedirect()->assertSessionHas('success', 'Offer deleted.');

    $this->assertSoftDeleted('collectible_offers', [
        'id' => $collectibleOffer->id,
    ]);
});

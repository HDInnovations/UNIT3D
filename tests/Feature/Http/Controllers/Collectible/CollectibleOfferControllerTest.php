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
use App\Models\CollectibleRequirement;
use App\Models\Group;
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

    $response = $this->actingAs($user)->get(route('collectibles.offer.create', $collectible));
    $response->assertStatus(200);
});

test('create a new collectible offer returns an ok response', function (): void {
    $user = User::factory()->create();

    $collectible = Collectible::factory()->create([
        'price'  => 1000,
        'resell' => 1,
    ]);
    $collectibleItem = CollectibleItem::factory()->create([
        'collectible_id' => $collectible->id,
        'user_id'        => $user->id,
    ]);

    $data = [
        'offer' => [
            'price' => 1400,
        ],
    ];

    $response = $this->actingAs($user)->post(route('collectibles.offer.store', ['collectible' => $collectible]), $data);

    $response->assertRedirect(route('collectibles.show', ['collectible' => $collectible]));
    $response->assertSessionHas('success', 'Offer created.');

    $this->assertDatabaseHas('collectible_offers', [
        'collectible_id' => $collectible->id,
        'user_id'        => $user->id,
        'price'          => 1400,
        'filled_at'      => null,
    ]);
});

test('create a new collectible offer when resell is disabled returns an error response', function (): void {
    $user = User::factory()->create();

    $collectible = Collectible::factory()->create([
        'price'  => 1000,
        'resell' => 0,
    ]);

    $collectibleItem = CollectibleItem::factory()->create([
        'collectible_id' => $collectible->id,
        'user_id'        => $user->id,
    ]);

    $data = [
        'offer' => [
            'price' => 1400,
        ],
    ];

    $response = $this->actingAs($user)->post(route('collectibles.offer.store', ['collectible' => $collectible]), $data);

    $response->assertRedirect(route('collectibles.show', ['collectible' => $collectible]));
    $response->assertSessionHas('errors');
    $this->assertContains('Reselling is disabled for this collectible.', session('errors')->all());

    $this->assertDatabaseMissing('collectible_offers', [
        'collectible_id' => $collectible->id,
        'user_id'        => $user->id,
        'price'          => 1400,
        'filled_at'      => null,
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

    $collectibleRequirement = CollectibleRequirement::factory()->create([
        'collectible_id'   => $collectible->id,
        'min_uploaded'     => null,
        'min_seedsize'     => null,
        'min_avg_seedtime' => null,
        'min_ratio'        => null,
        'min_age'          => null,
    ]);

    $response = $this->actingAs($buyer)->patch(route('collectibles.offer.update', ['collectibleOffer' => $collectibleOffer]));

    $response->assertRedirect(route('collectibles.show', ['collectible' => $collectible]));
    $response->assertSessionHas('success', 'Offer accepted.');

    $this->assertDatabaseHas('collectible_transactions', [
        'collectible_id' => $collectible->id,
        'seller_id'      => $seller->id,
        'buyer_id'       => $buyer->id,
        'price'          => 1100,
    ]);

    $this->assertDatabaseHas('collectible_offers', [
        'id' => $collectibleOffer->id,
    ]);
    $this->assertNotNull(
        DB::table('collectible_offers')->where('id', $collectibleOffer->id)->value('filled_at')
    );

    // Check seedbonus updated
    $seller->refresh();
    $buyer->refresh();
    expect($seller->seedbonus)->toEqual(1100);
    expect($buyer->seedbonus)->toEqual(50);
});

test('accept an offer with insufficient bon returns an error response', function (): void {
    $buyer = User::factory()->create([
        'seedbonus' => 0,
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

    $collectibleRequirement = CollectibleRequirement::factory()->create([
        'collectible_id'   => $collectible->id,
        'min_uploaded'     => null,
        'min_seedsize'     => null,
        'min_avg_seedtime' => null,
        'min_ratio'        => null,
        'min_age'          => null,
    ]);

    $response = $this->actingAs($buyer)->patch(route('collectibles.offer.update', ['collectibleOffer' => $collectibleOffer]));

    $response->assertRedirect(route('collectibles.show', ['collectible' => $collectible]));
    $response->assertSessionHas('errors');
    $this->assertContains('Not enough BON.', session('errors')->all());
});

test('accept own offer returns an error response', function (): void {
    $seller = User::factory()->create([
        'seedbonus' => 5000,
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

    $collectibleRequirement = CollectibleRequirement::factory()->create([
        'collectible_id'   => $collectible->id,
        'min_uploaded'     => null,
        'min_seedsize'     => null,
        'min_avg_seedtime' => null,
        'min_ratio'        => null,
        'min_age'          => null,
    ]);

    $response = $this->actingAs($seller)->patch(route('collectibles.offer.update', ['collectibleOffer' => $collectibleOffer]));

    $response->assertRedirect(route('collectibles.show', ['collectible' => $collectible]));
    $response->assertSessionHas('errors');
    $this->assertContains('You can not accept your own offer.', session('errors')->all());
});

test('accept offer on already owned item returns an error response', function (): void {
    $buyer = User::factory()->create([
        'seedbonus' => 1150,
    ]);
    $seller = User::factory()->create([
        'seedbonus' => 0,
    ]);

    $collectible = Collectible::factory()->create([
        'price' => 1000,
    ]);

    $collectibleItemSeller = CollectibleItem::factory()->create([
        'collectible_id' => $collectible->id,
        'user_id'        => $seller->id,
    ]);

    $collectibleItemBuyer = CollectibleItem::factory()->create([
        'collectible_id' => $collectible->id,
        'user_id'        => $buyer->id,
    ]);

    $collectibleOffer = CollectibleOffer::factory()->create([
        'collectible_id' => $collectible->id,
        'user_id'        => $seller->id,
        'price'          => 1100,
        'filled_at'      => null,
    ]);

    $collectibleRequirement = CollectibleRequirement::factory()->create([
        'collectible_id'   => $collectible->id,
        'min_uploaded'     => null,
        'min_seedsize'     => null,
        'min_avg_seedtime' => null,
        'min_ratio'        => null,
        'min_age'          => null,
    ]);

    $response = $this->actingAs($buyer)->patch(route('collectibles.offer.update', ['collectibleOffer' => $collectibleOffer]));

    $response->assertRedirect(route('collectibles.show', ['collectible' => $collectible]));
    $response->assertSessionHas('errors');
    $this->assertContains('You already own this item.', session('errors')->all());
});

test('seller deletes their own offer returns an ok response', function (): void {
    $user = User::factory()->create();

    $collectible = Collectible::factory()->create();

    $collectibleOffer = CollectibleOffer::factory()->create([
        'collectible_id' => $collectible->id,
        'user_id'        => $user->id,
    ]);

    $response = $this->actingAs($user)->delete(route('collectibles.offer.destroy', ['collectibleOffer' => $collectibleOffer]));

    $response->assertRedirect(route('collectibles.show', ['collectible' => $collectible]));
    $response->assertSessionHas('success', 'Offer deleted.');

    $this->assertSoftDeleted($collectibleOffer);
});

test('user deletes foreign offer returns an error response', function (): void {
    $user = User::factory()->create();
    $seller = User::factory()->create();

    $collectible = Collectible::factory()->create();

    $collectibleOffer = CollectibleOffer::factory()->create([
        'collectible_id' => $collectible->id,
        'user_id'        => $seller->id,
    ]);

    $response = $this->actingAs($user)->delete(route('collectibles.offer.destroy', ['collectibleOffer' => $collectibleOffer]));

    $response->assertStatus(403);

    $this->assertDatabaseHas('collectible_offers', [
        'id' => $collectibleOffer->id,
    ]);
});

test('modo can delete all offers returns an ok response', function (): void {
    $group = Group::factory()->create([
        'is_modo' => 1,
    ]);

    $moderator = User::factory()->create([
        'group_id' => $group->id,
    ]);
    $seller = User::factory()->create();

    $collectible = Collectible::factory()->create();
    $collectibleOffer = CollectibleOffer::factory()->create([
        'collectible_id' => $collectible->id,
        'user_id'        => $seller->id,
    ]);

    $response = $this->actingAs($moderator)->delete(route('collectibles.offer.destroy', ['collectibleOffer' => $collectibleOffer]));

    $response->assertRedirect(route('collectibles.show', ['collectible' => $collectible]));
    $response->assertSessionHas('success', 'Offer deleted.');

    $this->assertSoftDeleted($collectibleOffer);
});

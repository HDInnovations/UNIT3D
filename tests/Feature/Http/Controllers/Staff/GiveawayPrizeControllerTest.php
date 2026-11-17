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
 * @author     Roardom <roardom@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

use App\Models\Giveaway;
use App\Models\GiveawayPrize;

test('destroy returns an ok response', function (): void {
    $prize = GiveawayPrize::factory()->create();

    $this->delete(route('staff.giveaways.prizes.destroy', [$prize->giveaway, $prize]))
        ->assertRedirectToRoute('staff.giveaways.edit', $prize->giveaway);

    $this->assertModelMissing($prize);
});

test('store returns an ok response', function (): void {
    $giveaway = Giveaway::factory()->create();
    $prize = GiveawayPrize::factory()->make()->makeHidden('giveaway_id');

    $this->post(route('staff.giveaways.prizes.store', [$giveaway, $prize]), $prize->toArray())
        ->assertRedirectToRoute('staff.giveaways.edit', $giveaway);

    $this->assertDatabaseHas('giveaway_prizes', [
        ...$prize->toArray(),
        'giveaway_id' => $giveaway->id,
    ]);
});

test('update returns an ok response', function (): void {
    $prize = GiveawayPrize::factory()->create();
    $newPrize = GiveawayPrize::factory()->make()->makeHidden('giveaway_id');

    $this->patch(route('staff.giveaways.prizes.update', [$prize->giveaway, $prize]), $newPrize->toArray())
        ->assertRedirectToRoute('staff.giveaways.edit', [$prize->giveaway]);

    $this->assertDatabaseHas('giveaway_prizes', [
        ...$newPrize->toArray(),
        'giveaway_id' => $prize->giveaway_id,
    ]);
});

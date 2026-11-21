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
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

use App\Models\BonExchange;

test('create returns an ok response', function (): void {
    $this->get(route('staff.bon_exchanges.create'))
        ->assertOk()
        ->assertViewIs('Staff.bon-exchange.create');
});

test('destroy returns an ok response', function (): void {
    $bonExchange = BonExchange::factory()->create();

    $this->delete(route('staff.bon_exchanges.destroy', [$bonExchange]))
        ->assertRedirectToRoute('staff.bon_exchanges.index')
        ->assertSessionHas('success', 'Bon exchange successfully deleted');
    $this->assertModelMissing($bonExchange);
});

test('edit returns an ok response', function (): void {
    $bonExchange = BonExchange::factory()->create();

    $this->get(route('staff.bon_exchanges.edit', [$bonExchange]))
        ->assertOk()
        ->assertViewIs('Staff.bon-exchange.edit')
        ->assertViewHas('bonExchange', $bonExchange);
});

test('index returns an ok response', function (): void {
    $bonExchanges = BonExchange::factory()->times(3)->create();

    $this->get(route('staff.bon_exchanges.index'))
        ->assertOk()
        ->assertViewIs('Staff.bon-exchange.index')
        ->assertViewHas('bonExchanges', $bonExchanges);
});

test('store returns an ok response', function (): void {
    $bonExchange = BonExchange::factory()->make();
    $types = ['upload', 'download', 'personal_freeleech', 'invite'];

    $this->post(route('staff.bon_exchanges.store'), [
        'description' => $bonExchange->description,
        'value'       => $bonExchange->value,
        'cost'        => $bonExchange->cost,
        'type'        => $types[array_rand($types)],
    ])
        ->assertRedirectToRoute('staff.bon_exchanges.index')
        ->assertSessionHas('success', 'Bon exchange successfully added');
});

test('update returns an ok response', function (): void {
    $bonExchange = BonExchange::factory()->create();
    $newBonExchange = BonExchange::factory()->make();
    $types = ['upload', 'download', 'personal_freeleech', 'invite'];

    $response = $this->patch(route('staff.bon_exchanges.update', [$bonExchange]), [
        'description' => $bonExchange->description,
        'value'       => $bonExchange->value,
        'cost'        => $bonExchange->cost,
        'type'        => $types[array_rand($types)],
    ]);

    $response->assertRedirectToRoute('staff.bon_exchanges.index');
    $response->assertSessionHas('success', 'Bon exchange successfully modified');
});

// test cases...

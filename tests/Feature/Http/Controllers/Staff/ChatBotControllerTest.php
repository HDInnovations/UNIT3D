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

use App\Models\Bot;
use Database\Seeders\GroupSeeder;

test('destroy returns an ok response', function (): void {
    $bot = Bot::factory()->create([
        'is_protected' => false,
    ]);

    $this->delete(route('staff.bots.destroy', ['bot' => $bot]))
        ->assertRedirect(route('staff.bots.index'))->assertSessionHas('success', 'The Humans Vs Machines War has begun! Humans: 1 and Bots: 0');

    $this->assertModelMissing($bot);
});

test('destroy aborts with a 403', function (): void {
    $this->seed(GroupSeeder::class);

    $bot = Bot::factory()->create([
        'is_protected' => true,
    ]);

    $this->delete(route('staff.bots.destroy', [$bot]))
        ->assertForbidden();
});

test('disable returns an ok response', function (): void {
    $bot = Bot::factory()->create([
        'active' => true,
    ]);

    $this->post(route('staff.bots.disable', [$bot]), [
        'active' => false,
    ])
        ->assertRedirect(route('staff.bots.index'))->assertSessionHas('success', 'The bot has been disabled');
});

test('edit returns an ok response', function (): void {
    $bot = Bot::factory()->create();

    $this->get(route('staff.bots.edit', [$bot]))
        ->assertOk()
        ->assertViewIs('Staff.chat.bot.edit')
        ->assertViewHas('bot', $bot);
});

test('enable returns an ok response', function (): void {
    $bot = Bot::factory()->create([
        'active' => false,
    ]);

    $this->post(route('staff.bots.enable', [$bot]), [
        'active' => true,
    ])
        ->assertRedirect(route('staff.bots.index'))->assertSessionHas('success', 'The bot has been enabled');
});

test('index returns an ok response', function (): void {
    $this->get(route('staff.bots.index'))
        ->assertOk()
        ->assertViewIs('Staff.chat.bot.index');
});

test('update returns an ok response', function (): void {
    $bot = Bot::factory()->create([
        'is_protected' => false,
    ]);

    $response = $this->patch(route('staff.bots.update', [$bot]), [
        'position'     => $bot->position,
        'name'         => $bot->name,
        'command'      => $bot->command,
        'color'        => $bot->color,
        'icon'         => $bot->icon,
        'emoji'        => $bot->emoji,
        'help'         => $bot->help,
        'active'       => true,
        'is_protected' => $bot->is_protected,
        'is_nerdbot'   => $bot->is_nerdbot,
        'is_systembot' => $bot->is_systembot,
    ]);
    $response->assertRedirect(route('staff.bots.index'))->assertSessionHas('success', 'The bot has been updated');
});

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

use App\Models\AutomaticTorrentFreeleech;

test('create returns an ok response', function (): void {
    $this->get(route('staff.automatic_torrent_freeleeches.create'))
        ->assertOk()
        ->assertViewIs('Staff.automatic-torrent-freeleech.create');
});

test('destroy returns an ok response', function (): void {
    $automaticTorrentFreeleech = AutomaticTorrentFreeleech::factory()->create();

    $this->delete(route('staff.automatic_torrent_freeleeches.destroy', [$automaticTorrentFreeleech]))
        ->assertRedirectToRoute('staff.automatic_torrent_freeleeches.index')
        ->assertSessionHas('success', 'Automatic torrent freeleech successfully deleted');

    $this->assertModelMissing($automaticTorrentFreeleech);
});

test('edit returns an ok response', function (): void {
    $automaticTorrentFreeleech = AutomaticTorrentFreeleech::factory()->create();

    $this->get(route('staff.automatic_torrent_freeleeches.edit', [$automaticTorrentFreeleech]))
        ->assertOk()
        ->assertViewIs('Staff.automatic-torrent-freeleech.edit')
        ->assertViewHas('automaticTorrentFreeleech', $automaticTorrentFreeleech);
});

test('index returns an ok response', function (): void {
    $this->get(route('staff.automatic_torrent_freeleeches.index'))
        ->assertOk()
        ->assertViewIs('Staff.automatic-torrent-freeleech.index')
        ->assertViewHas('automaticTorrentFreeleeches');
});

test('store returns an ok response', function (): void {
    $this->post(route('staff.automatic_torrent_freeleeches.store'), [
        'position'             => 1,
        'freeleech_percentage' => 50,
    ])
        ->assertRedirectToRoute('staff.automatic_torrent_freeleeches.index')
        ->assertSessionHas('success', 'Automatic torrent freeleech successfully added');
});

test('update returns an ok response', function (): void {
    $automaticTorrentFreeleech = AutomaticTorrentFreeleech::factory()->create();

    $this->patch(route('staff.automatic_torrent_freeleeches.update', [$automaticTorrentFreeleech]), [
        'position'             => 5,
        'freeleech_percentage' => 25,
    ])
        ->assertRedirectToRoute('staff.automatic_torrent_freeleeches.index')
        ->assertSessionHas('success', 'Automatic torrent freeleech successfully modified');
});

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

use App\Http\Livewire\NoteSearch;
use App\Models\Note;

test('update returns an ok response', function (): void {
    $note = Note::factory()->create();
    $newNote = Note::factory()->make();

    $this->patch(route('staff.notes.update', [$note]), $newNote->toArray())
        ->assertRedirectBack()
        ->assertSessionHas('success', 'Success');

    $this->assertDatabaseHas('user_notes', [
        'id'      => $note->id,
        'message' => $newNote->message,
    ]);
});

test('index returns an ok response', function (): void {
    $this->get(route('staff.notes.index'))
        ->assertOk()
        ->assertViewIs('Staff.note.index')
        ->assertSeeLivewire(NoteSearch::class);
});

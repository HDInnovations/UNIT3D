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
 * @author     Obi-wana
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

use App\Models\UploadContest;

test('index upload contests returns an ok response', function (): void {
    $uploadContests = UploadContest::factory()->times(3)->create();

    $this->get(route('staff.upload_contests.index'))
        ->assertOk()
        ->assertViewIs('Staff.upload-contest.index')
        ->assertViewHas('uploadContests');
});

test('edit upload contests returns an ok response', function (): void {
    $uploadContest = UploadContest::factory()->create();

    $this->get(route('staff.upload_contests.edit', [$uploadContest]))
        ->assertOk()
        ->assertViewIs('Staff.upload-contest.edit')
        ->assertViewHas('uploadContest');
});

test('create upload contests returns an ok response', function (): void {
    $this->get(route('staff.upload_contests.create'))
        ->assertOk()
        ->assertViewIs('Staff.upload-contest.create');
});

test('store a new upload contest returns an ok response', function (): void {
    $uploadContest = UploadContest::factory()->make()->makeHidden('awarded', 'active');

    $this->post(route('staff.upload_contests.store'), $uploadContest->toArray())
        ->assertRedirect(route('staff.upload_contests.index'));
    $this->assertDatabaseHas('upload_contests', $uploadContest->toArray());
});

test('update an upload contest returns an ok response', function (): void {
    $uploadContest = UploadContest::factory()->create();
    $newUploadContest = UploadContest::factory()->make()->makeHidden('awarded');

    $this->patch(route('staff.upload_contests.update', [$uploadContest]), $newUploadContest->toArray())
        ->assertRedirect(route('staff.upload_contests.index'));
    $this->assertDatabaseHas('upload_contests', [
        'id' => $uploadContest->id,
        ...$newUploadContest->toArray(),
    ]);
});

test('destroy an upload contest returns an ok response', function (): void {
    $uploadContest = UploadContest::factory()->create();

    $this->delete(route('staff.upload_contests.destroy', $uploadContest))
        ->assertRedirect(route('staff.upload_contests.index'));
    $this->assertModelMissing($uploadContest);
});

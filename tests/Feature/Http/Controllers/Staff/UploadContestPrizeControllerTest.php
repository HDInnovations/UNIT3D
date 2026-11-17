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

use App\Models\UploadContest;
use App\Models\UploadContestPrize;

test('destroy returns an ok response', function (): void {
    $prize = UploadContestPrize::factory()->create();

    $this->delete(route('staff.upload_contests.prizes.destroy', [$prize->contest, $prize]))
        ->assertRedirectToRoute('staff.upload_contests.edit', $prize->contest);

    $this->assertModelMissing($prize);
});

test('store returns an ok response', function (): void {
    $uploadContest = UploadContest::factory()->create();
    $prize = UploadContestPrize::factory()->make()->makeHidden('upload_contest_id');

    $this->post(route('staff.upload_contests.prizes.store', [$uploadContest, $prize]), $prize->toArray())
        ->assertRedirectToRoute('staff.upload_contests.edit', $uploadContest);

    $this->assertDatabaseHas('upload_contest_prizes', [
        ...$prize->toArray(),
        'upload_contest_id' => $uploadContest->id,
    ]);
});

test('update returns an ok response', function (): void {
    $prize = UploadContestPrize::factory()->create();
    $newPrize = UploadContestPrize::factory()->make()->makeHidden('upload_contest_id');

    $this->patch(route('staff.upload_contests.prizes.update', [$prize->contest, $prize]), $newPrize->toArray())
        ->assertRedirectToRoute('staff.upload_contests.edit', [$prize->contest]);

    $this->assertDatabaseHas('upload_contest_prizes', [
        ...$newPrize->toArray(),
        'upload_contest_id' => $prize->upload_contest_id,
    ]);
});

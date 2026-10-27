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

use App\Console\Commands\AutoRewardUploadContestPrize;
use App\Models\UploadContest;
use App\Models\Torrent;
use App\Models\User;

/**
 * @see AutoRewardUploadContestPrize
 */
it('runs successfully', function (): void {
    $this->artisan(AutoRewardUploadContestPrize::class)
        ->assertExitCode(0)
        ->run();
});

it('rewards the top competitors in active upload contests', function (): void {
    $uploader1 = User::factory()->create([
        'seedbonus' => 0,
        'fl_tokens' => 0,
    ]);
    $uploader2 = User::factory()->create([
        'seedbonus' => 0,
        'fl_tokens' => 0,
    ]);
    $uploader3 = User::factory()->create([
        'seedbonus' => 0,
        'fl_tokens' => 0,
    ]);

    // Uploads for each user
    $torrentsUploader1 = Torrent::factory()->times(3)->create([
        'user_id'    => $uploader1->id,
        'anon'       => false,
        'created_at' => now()->subDays(3),
    ]);
    $torrentsUploader2 = Torrent::factory()->times(6)->create([
        'user_id'    => $uploader2->id,
        'anon'       => false,
        'created_at' => now()->subDays(3),
    ]);
    $torrentsUploader3 = Torrent::factory()->times(1)->create([
        'user_id'    => $uploader3->id,
        'anon'       => false,
        'created_at' => now()->subDays(3),
    ]);

    // Contest
    $uploadContest = UploadContest::factory()->create([
        'active'    => true,
        'awarded'   => false,
        'starts_at' => now()->subDays(7)->format('Y-m-d'),
        'ends_at'   => now()->subDays(1)->format('Y-m-d'),
    ]);

    // Prizes for 1st place
    $uploadContest->prizes()->create([
        'position' => 1,
        'amount'   => 100,
        'type'     => 'bon',
    ]);
    $uploadContest->prizes()->create([
        'position' => 1,
        'amount'   => 10,
        'type'     => 'fl_tokens',
    ]);
    // Prizes for 2nd place
    $uploadContest->prizes()->create([
        'position' => 2,
        'amount'   => 5,
        'type'     => 'fl_tokens',
    ]);

    // Run command
    $this->artisan(AutoRewardUploadContestPrize::class)->assertExitCode(0);

    // Assert
    $this->assertDatabaseHas('upload_contests', [
        'id'      => $uploadContest->id,
        'awarded' => 1,
    ]);

    $this->assertDatabaseHas('upload_contest_winners', [
        'upload_contest_id' => $uploadContest->id,
        'user_id'           => $uploader2->id,
        'place_number'      => 1,
        'uploads'           => 6,
    ]);
    $this->assertDatabaseHas('upload_contest_winners', [
        'upload_contest_id' => $uploadContest->id,
        'user_id'           => $uploader1->id,
        'place_number'      => 2,
        'uploads'           => 3,
    ]);
    $this->assertDatabaseHas('upload_contest_winners', [
        'upload_contest_id' => $uploadContest->id,
        'user_id'           => $uploader3->id,
        'place_number'      => 3,
        'uploads'           => 1,
    ]);

    $this->assertDatabaseHas('users', [
        'id'        => $uploader1->id,
        'seedbonus' => 0,
        'fl_tokens' => 5,
    ]);
    $this->assertDatabaseHas('users', [
        'id'        => $uploader2->id,
        'seedbonus' => 100,
        'fl_tokens' => 10,
    ]);
    $this->assertDatabaseHas('users', [
        'id'        => $uploader3->id,
        'seedbonus' => 0,
        'fl_tokens' => 0,
    ]);
});

it('handles ties between competitors in active upload contests', function (): void {
    $uploader1 = User::factory()->create([
        'seedbonus' => 0,
        'fl_tokens' => 0,
    ]);
    $uploader2 = User::factory()->create([
        'seedbonus' => 0,
        'fl_tokens' => 0,
    ]);
    $uploader3 = User::factory()->create([
        'seedbonus' => 0,
        'fl_tokens' => 0,
    ]);
    $uploader4 = User::factory()->create([
        'seedbonus' => 0,
        'fl_tokens' => 0,
    ]);

    // Uploads for each user
    $torrentsUploader1 = Torrent::factory()->times(6)->create([
        'user_id'    => $uploader1->id,
        'anon'       => false,
        'created_at' => now()->subDays(3),
    ]);
    $torrentsUploader2 = Torrent::factory()->times(6)->create([
        'user_id'    => $uploader2->id,
        'anon'       => false,
        'created_at' => now()->subDays(4),
    ]);
    $torrentsUploader3 = Torrent::factory()->times(1)->create([
        'user_id'    => $uploader3->id,
        'anon'       => false,
        'created_at' => now()->subDays(4),
    ]);
    $torrentsUploader4 = Torrent::factory()->times(1)->create([
        'user_id'    => $uploader4->id,
        'anon'       => false,
        'created_at' => now()->subDays(3),
    ]);

    // Contest
    $uploadContest = UploadContest::factory()->create([
        'active'    => true,
        'awarded'   => false,
        'starts_at' => now()->subDays(7)->format('Y-m-d'),
        'ends_at'   => now()->subDays(1)->format('Y-m-d'),
    ]);

    // Prizes for 1st place
    $uploadContest->prizes()->create([
        'position' => 1,
        'amount'   => 100,
        'type'     => 'bon',
    ]);
    $uploadContest->prizes()->create([
        'position' => 1,
        'amount'   => 10,
        'type'     => 'fl_tokens',
    ]);
    // Prizes for 2nd place
    $uploadContest->prizes()->create([
        'position' => 2,
        'amount'   => 5,
        'type'     => 'fl_tokens',
    ]);
    // Prizes for 3rd place
    $uploadContest->prizes()->create([
        'position' => 3,
        'amount'   => 1,
        'type'     => 'fl_tokens',
    ]);

    // Run command
    $this->artisan(AutoRewardUploadContestPrize::class)->assertExitCode(0);

    // Assert
    $this->assertDatabaseHas('upload_contests', [
        'id'      => $uploadContest->id,
        'awarded' => 1,
    ]);

    $this->assertDatabaseHas('upload_contest_winners', [
        'upload_contest_id' => $uploadContest->id,
        'user_id'           => $uploader2->id,
        'place_number'      => 1,
        'uploads'           => 6,
    ]);
    $this->assertDatabaseHas('upload_contest_winners', [
        'upload_contest_id' => $uploadContest->id,
        'user_id'           => $uploader1->id,
        'place_number'      => 2,
        'uploads'           => 6,
    ]);
    $this->assertDatabaseHas('upload_contest_winners', [
        'upload_contest_id' => $uploadContest->id,
        'user_id'           => $uploader3->id,
        'place_number'      => 3,
        'uploads'           => 1,
    ]);

    $this->assertDatabaseHas('users', [
        'id'        => $uploader1->id,
        'seedbonus' => 0,
        'fl_tokens' => 5,
    ]);
    $this->assertDatabaseHas('users', [
        'id'        => $uploader2->id,
        'seedbonus' => 100,
        'fl_tokens' => 10,
    ]);
    $this->assertDatabaseHas('users', [
        'id'        => $uploader3->id,
        'seedbonus' => 0,
        'fl_tokens' => 1,
    ]);
    $this->assertDatabaseHas('users', [
        'id'        => $uploader4->id,
        'seedbonus' => 0,
        'fl_tokens' => 0,
    ]);
});

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
use App\Models\Torrent;
use App\Models\User;

test('show an upload contest returns an ok response', function (): void {
    $user = User::factory()->create();

    $uploader1 = User::factory()->create();
    $uploader2 = User::factory()->create();
    $uploader3 = User::factory()->create();

    $torrentsUploader1 = Torrent::factory()->times(3)->create([
        'user_id'    => $uploader1->id,
        'anon'       => false,
        'created_at' => now(),
    ]);
    $torrentsUploader2 = Torrent::factory()->times(6)->create([
        'user_id'    => $uploader2->id,
        'anon'       => false,
        'created_at' => now(),
    ]);
    $torrentsUploader3 = Torrent::factory()->times(1)->create([
        'user_id'    => $uploader3->id,
        'anon'       => false,
        'created_at' => now(),
    ]);

    $uploadContest = UploadContest::factory()->create([
        'active'    => true,
        'awarded'   => false,
        'starts_at' => now()->subDays(2)->format('Y-m-d'),
        'ends_at'   => now()->addDays(2)->format('Y-m-d'),
    ]);

    $response = $this->actingAs($user)->get(route('upload_contests.show', $uploadContest));

    $response->assertOk();
    $response->assertViewIs('upload-contest.show');
    $response->assertViewHas('uploadContest', $uploadContest);

    $uploaders = $response->viewData('uploaders');
    $this->assertCount(3, $uploaders);
    $this->assertEquals($uploader2->id, $uploaders[0]->user_id);
    $this->assertEquals($uploader1->id, $uploaders[1]->user_id);
    $this->assertEquals($uploader3->id, $uploaders[2]->user_id);
});

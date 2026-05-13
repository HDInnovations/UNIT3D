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

use App\Models\Group;
use App\Models\User;

describe('user model', function (): void {
    it('allows trusted users to skip torrent moderation', function (): void {
        $group = Group::factory()->create(['is_trusted' => true]);
        $user = User::factory()->create(['group_id' => $group->id]);

        expect($user->canSkipTorrentModeration())->toBeTrue();
    });

    it('does not skip torrent moderation when the user is forced through mod queue', function (): void {
        $group = Group::factory()->create(['is_trusted' => true]);
        $user = User::factory()->create([
            'group_id'        => $group->id,
            'force_mod_queue' => true,
        ]);

        expect($user->canSkipTorrentModeration())->toBeFalse();
    });

    it('does not skip torrent moderation when the user opts in', function (): void {
        $group = Group::factory()->create(['is_trusted' => true]);
        $user = User::factory()->create(['group_id' => $group->id]);

        expect($user->canSkipTorrentModeration(modQueueOptIn: true))->toBeFalse();
    });

    it('does not skip torrent moderation for untrusted users', function (): void {
        $group = Group::factory()->create(['is_trusted' => false]);
        $user = User::factory()->create(['group_id' => $group->id]);

        expect($user->canSkipTorrentModeration())->toBeFalse();
    });
});

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

use App\Http\Controllers\Staff\GroupController;
use App\Http\Middleware\UpdateLastAction;
use App\Http\Requests\Staff\StoreGroupRequest;
use App\Http\Requests\Staff\UpdateGroupRequest;
use App\Models\Forum;
use App\Models\Group;
use Illuminate\Routing\Middleware\ThrottleRequestsWithRedis;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function (): void {
    $this->withoutMiddleware([
        ThrottleRequestsWithRedis::class,
        UpdateLastAction::class,
    ]);
});

test('create returns an ok response', function (): void {
    $this->get(route('staff.groups.create'))
        ->assertOk()
        ->assertViewIs('Staff.group.create');
});

test('edit returns an ok response', function (): void {
    $group = Group::factory()->create();

    $this->get(route('staff.groups.edit', [$group]))
        ->assertOk()
        ->assertViewIs('Staff.group.edit')
        ->assertViewHas('group', $group);
});

test('index returns an ok response', function (): void {
    Group::factory()->times(3)->create();

    $this->get(route('staff.groups.index'))
        ->assertOk()
        ->assertViewIs('Staff.group.index')
        ->assertViewHas('groups');
});

test('store validates with a form request', function (): void {
    $this->assertActionUsesFormRequest(
        GroupController::class,
        'store',
        StoreGroupRequest::class
    );
});

test('store returns an ok response', function (): void {
    $group = Group::factory()->make();
    $forum = Forum::factory()->create();

    $this->post(route('staff.groups.store'), [
        'group' => [
            'name'             => $group->name,
            'position'         => $group->position,
            'level'            => $group->level,
            'color'            => $group->color,
            'icon'             => $group->icon,
            'effect'           => $group->effect,
            'is_uploader'      => $group->is_uploader,
            'is_internal'      => $group->is_internal,
            'is_owner'         => $group->is_owner,
            'is_admin'         => $group->is_admin,
            'is_modo'          => $group->is_modo,
            'is_torrent_modo'  => $group->is_torrent_modo,
            'is_editor'        => $group->is_editor,
            'is_trusted'       => $group->is_trusted,
            'is_immune'        => $group->is_immune,
            'is_freeleech'     => $group->is_freeleech,
            'is_double_upload' => $group->is_double_upload,
            'is_refundable'    => $group->is_refundable,
            'can_chat'         => $group->can_chat,
            'can_comment'      => $group->can_comment,
            'can_invite'       => $group->can_invite,
            'can_request'      => $group->can_request,
            'can_upload'       => $group->can_upload,
            'is_incognito'     => $group->is_incognito,
            'autogroup'        => $group->autogroup,
        ],
        'permissions' => [
            [
                'forum_id'    => $forum->id,
                'read_topic'  => true,
                'start_topic' => true,
                'reply_topic' => true,
            ],
        ]
    ])
        ->assertRedirect(route('staff.groups.index'))
        ->assertSessionHasNoErrors();

    assertDatabaseHas('groups', [
        'name'     => $group->name,
        'position' => $group->position,
    ]);
});

test('store converts autogroup requirement units', function (): void {
    $group = Group::factory()->make([
        'name'      => 'Unit Converted',
        'autogroup' => true,
    ]);
    $forum = Forum::factory()->create();

    $this->post(route('staff.groups.store'), [
        'group' => [
            'name'                  => $group->name,
            'position'              => $group->position,
            'level'                 => $group->level,
            'color'                 => $group->color,
            'icon'                  => $group->icon,
            'effect'                => $group->effect,
            'is_uploader'           => $group->is_uploader,
            'is_internal'           => $group->is_internal,
            'is_owner'              => $group->is_owner,
            'is_admin'              => $group->is_admin,
            'is_modo'               => $group->is_modo,
            'is_torrent_modo'       => $group->is_torrent_modo,
            'is_editor'             => $group->is_editor,
            'is_trusted'            => $group->is_trusted,
            'is_immune'             => $group->is_immune,
            'is_freeleech'          => $group->is_freeleech,
            'is_double_upload'      => $group->is_double_upload,
            'can_chat'              => $group->can_chat,
            'can_comment'           => $group->can_comment,
            'can_invite'            => $group->can_invite,
            'can_request'           => $group->can_request,
            'can_upload'            => $group->can_upload,
            'is_incognito'          => $group->is_incognito,
            'autogroup'             => true,
            'min_uploaded'          => 5,
            'min_uploaded_unit'     => 'gb',
            'min_seedsize'          => 2,
            'min_seedsize_unit'     => 'tb',
            'min_age'               => 3,
            'min_age_unit'          => 'weeks',
            'min_avg_seedtime'      => 4,
            'min_avg_seedtime_unit' => 'days',
            'min_ratio'             => 1.5,
            'min_uploads'           => 12,
        ],
        'permissions' => [
            [
                'forum_id'    => $forum->id,
                'read_topic'  => true,
                'start_topic' => true,
                'reply_topic' => true,
            ],
        ],
    ])
        ->assertRedirect(route('staff.groups.index'))
        ->assertSessionHasNoErrors();

    assertDatabaseHas('groups', [
        'name'             => 'Unit Converted',
        'min_uploaded'     => 5 * 1024 ** 3,
        'min_seedsize'     => 2 * 1024 ** 4,
        'min_age'          => 3 * 604800,
        'min_avg_seedtime' => 4 * 86400,
        'min_uploads'      => 12,
    ]);
});

test('update validates with a form request', function (): void {
    $this->assertActionUsesFormRequest(
        GroupController::class,
        'update',
        UpdateGroupRequest::class
    );
});

test('update returns an ok response', function (): void {
    $group = Group::factory()->create();
    $forum = Forum::factory()->create();

    $this->patch(route('staff.groups.update', ['group' => $group]), [
        'group' => [
            ...$group->toArray(),
            'name'     => 'new name',
            'position' => -2,
            'level'    => 1000,
        ],
        'permissions' => [
            [
                'forum_id'    => $forum->id,
                'read_topic'  => true,
                'start_topic' => true,
                'reply_topic' => true,
            ],
        ]
    ])
        ->assertRedirect(route('staff.groups.index'))
        ->assertSessionHasNoErrors();

    assertDatabaseHas('groups', [
        'name'     => 'new name',
        'slug'     => 'new-name',
        'position' => -2,
        'level'    => 1000,
    ]);

    assertDatabaseHas('forum_permissions', [
        'forum_id'    => $forum->id,
        'read_topic'  => true,
        'start_topic' => true,
        'reply_topic' => true,
    ]);
});

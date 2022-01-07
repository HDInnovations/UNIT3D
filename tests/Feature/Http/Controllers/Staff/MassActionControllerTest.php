<?php

namespace Tests\Feature\Http\Controllers\Staff;

use App\Models\Group;
use App\Models\PrivateMessage;
use App\Models\User;
use Database\Seeders\GroupsTableSeeder;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Staff\MassActionController
 */
class MassActionControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function createStaffUser()
    {
        return User::factory()->create([
            'group_id' => fn () => Group::factory()->create([
                'is_owner' => true,
                'is_admin' => true,
                'is_modo'  => true,
            ])->id,
        ]);
    }

    public function testCreateReturnsAnOkResponse()
    {
        $this->seed(GroupsTableSeeder::class);

        $user = $this->createStaffUser();

        $response = $this->actingAs($user)->get(route('staff.mass-pm.create'));

        $response->assertOk();
        $response->assertViewIs('Staff.masspm.index');
    }

    public function testStoreReturnsAnOkResponse()
    {
        $this->seed(GroupsTableSeeder::class);

        $user = $this->createStaffUser();
        $message = PrivateMessage::factory()->create();

        $response = $this->actingAs($user)->post(route('staff.mass-pm.store'), [
            'sender_id'   => $user->id,
            'receiver_id' => $message->receiver_id,
            'subject'     => $message->subject,
            'message'     => $message->message,
            'read'        => $message->read,
            'related_to'  => $message->related_to,
        ]);

        $response->assertRedirect(route('staff.mass-pm.create'));
    }

    public function testUpdateReturnsAnOkResponse()
    {
        $this->seed(GroupsTableSeeder::class);

        $user = $this->createStaffUser();

        $response = $this->actingAs($user)->get(route('staff.mass-actions.validate'));

        $response->assertRedirect(route('staff.dashboard.index'));
    }
}

<?php

namespace Tests\Feature\Http\Controllers\Staff;

use App\Models\Group;
use App\Models\User;
use Database\Seeders\GroupsTableSeeder;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Staff\GiftController
 */
class GiftControllerTest extends TestCase
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

    public function testIndexReturnsAnOkResponse()
    {
        $this->seed(GroupsTableSeeder::class);

        $user = $this->createStaffUser();

        $response = $this->actingAs($user)->get(route('staff.gifts.index'));

        $response->assertOk();
        $response->assertViewIs('Staff.gift.index');
    }

    public function testStoreReturnsAnOkResponse()
    {
        $this->seed(GroupsTableSeeder::class);

        $staff = $this->createStaffUser();
        $user = User::factory()->create();

        $response = $this->actingAs($staff)->post(route('staff.gifts.store'), [
            'username'  => $user->username,
            'seedbonus' => '100',
            'invites'   => '100',
            'fl_tokens' => '100',
        ]);

        $response->assertRedirect(route('staff.gifts.index'));
    }
}

<?php

declare(strict_types=1);

use App\Http\Livewire\UserSearch;
use App\Models\Application;
use App\Models\Invite;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->asStaffUser();
});

test('email search includes users invites and applications', function (): void {
    $matchingUser = User::factory()->create([
        'email'    => 'target@example.com',
        'username' => 'matching-user',
    ]);
    $unmatchedUser = User::factory()->create([
        'email'    => 'unmatched-user@example.com',
        'username' => 'unmatched-user',
    ]);
    $matchingInvite = Invite::factory()->create([
        'email' => 'target@example.com',
        'code'  => 'MATCHINGINVITE',
    ]);
    $unmatchedInvite = Invite::factory()->create([
        'email' => 'unmatched-invite@example.com',
        'code'  => 'UNMATCHEDINVITE',
    ]);
    $matchingApplication = Application::factory()->create([
        'email' => 'target@example.com',
        'type'  => 'Matching application',
    ]);
    $unmatchedApplication = Application::factory()->create([
        'email' => 'unmatched-application@example.com',
        'type'  => 'Unmatched application',
    ]);

    Livewire::test(UserSearch::class)
        ->set('email', 'target@example.com')
        ->assertSee($matchingUser->username)
        ->assertSee($matchingInvite->code)
        ->assertSee($matchingApplication->type)
        ->assertDontSee($unmatchedUser->username)
        ->assertDontSee($unmatchedInvite->code)
        ->assertDontSee($unmatchedApplication->type);
});

test('email search source toggles hide matching sections', function (): void {
    $matchingUser = User::factory()->create([
        'email'    => 'target@example.com',
        'username' => 'matching-user',
    ]);
    $matchingInvite = Invite::factory()->create([
        'email' => 'target@example.com',
        'code'  => 'MATCHINGINVITE',
    ]);
    $matchingApplication = Application::factory()->create([
        'email' => 'target@example.com',
        'type'  => 'Matching application',
    ]);

    Livewire::test(UserSearch::class)
        ->set('email', 'target@example.com')
        ->set('searchEmailUsers', false)
        ->set('searchEmailInvites', false)
        ->set('searchEmailApplications', false)
        ->assertDontSee($matchingUser->username)
        ->assertDontSee($matchingInvite->code)
        ->assertDontSee($matchingApplication->type);
});

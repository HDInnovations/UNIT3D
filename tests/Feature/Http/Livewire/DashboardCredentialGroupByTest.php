<?php

declare(strict_types=1);

use App\Http\Livewire\ApikeySearch;
use App\Http\Livewire\EmailUpdateSearch;
use App\Http\Livewire\PasskeySearch;
use App\Http\Livewire\RsskeySearch;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

test('staff credential searches can group keys by user', function (string $component, string $table, string $contentPrefix, string $heading): void {
    $staff = User::factory()->create();
    $user = User::factory()->create(['username' => $contentPrefix.'-owner']);

    DB::table($table)->insert([
        [
            'user_id'    => $user->id,
            'content'    => $contentPrefix.'-active',
            'created_at' => now()->subDays(2),
            'deleted_at' => null,
        ],
        [
            'user_id'    => $user->id,
            'content'    => $contentPrefix.'-deleted',
            'created_at' => now()->subDay(),
            'deleted_at' => now(),
        ],
    ]);

    Livewire::actingAs($staff)
        ->test($component)
        ->set('groupBy', 'user_id')
        ->assertSet('sortField', 'created_at_max')
        ->assertSee($contentPrefix.'-owner')
        ->assertSee($heading)
        ->assertSeeHtml('<td>2</td>')
        ->assertSeeHtml('<td>1</td>')
        ->assertDontSee($contentPrefix.'-active')
        ->assertDontSee($contentPrefix.'-deleted');
})->with([
    'api keys' => [ApikeySearch::class, 'apikeys', 'api-key', 'API keys'],
    'rss keys' => [RsskeySearch::class, 'rsskeys', 'rss-key', 'RSS keys'],
    'passkeys' => [PasskeySearch::class, 'passkeys', 'pass-key', 'Passkeys'],
]);

test('staff email update search can group rows by user', function (): void {
    $staff = User::factory()->create();
    $user = User::factory()->create(['username' => 'email-update-owner']);

    DB::table('email_updates')->insert([
        [
            'user_id'    => $user->id,
            'created_at' => now()->subDays(2),
            'deleted_at' => null,
        ],
        [
            'user_id'    => $user->id,
            'created_at' => now()->subDay(),
            'deleted_at' => now(),
        ],
    ]);

    Livewire::actingAs($staff)
        ->test(EmailUpdateSearch::class)
        ->set('groupBy', 'user_id')
        ->assertSet('sortField', 'created_at_max')
        ->assertSee('email-update-owner')
        ->assertSee('Email updates')
        ->assertSeeHtml('<td>2</td>')
        ->assertSeeHtml('<td>1</td>');
});

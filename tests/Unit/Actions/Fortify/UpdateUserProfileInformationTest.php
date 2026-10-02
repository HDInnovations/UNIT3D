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

use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

describe('UpdateUserProfileInformation', function (): void {
    it('fails because User model uses username not name', function (): void {
        $user = User::factory()->create([
            'username' => fake()->userName,
            'email' => fake()->freeEmail,
        ]);

        $action = new UpdateUserProfileInformation();
        
        // This action is broken because it expects 'name' field but User model uses 'username'
        expect(fn() => $action->update($user, [
            'name' => fake()->name,
            'email' => fake()->freeEmail,
        ]))->toThrow(QueryException::class);
    });

    it('validates name field is required', function (): void {
        $user = User::factory()->create();
        $action = new UpdateUserProfileInformation();
        
        expect(fn() => $action->update($user, [
            'email' => fake()->freeEmail,
        ]))->toThrow(ValidationException::class);
    });

    it('validates email field is required', function (): void {
        $user = User::factory()->create();
        $action = new UpdateUserProfileInformation();
        
        expect(fn() => $action->update($user, [
            'name' => fake()->name,
        ]))->toThrow(ValidationException::class);
    });

    it('validates email format', function (): void {
        $user = User::factory()->create();
        $action = new UpdateUserProfileInformation();
        
        expect(fn() => $action->update($user, [
            'name' => fake()->name,
            'email' => 'invalid-email',
        ]))->toThrow(ValidationException::class);
    });

    it('validates email uniqueness', function (): void {
        $existingEmail = fake()->freeEmail;
        $existingUser = User::factory()->create(['email' => $existingEmail]);
        $user = User::factory()->create(['email' => fake()->freeEmail]);
        
        $action = new UpdateUserProfileInformation();
        
        expect(fn() => $action->update($user, [
            'name' => fake()->name,
            'email' => $existingEmail,
        ]))->toThrow(ValidationException::class);
    });
});

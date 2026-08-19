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

use App\Actions\Fortify\UpdateUserPassword;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

describe('UpdateUserPassword', function (): void {
    it('updates user password with valid input', function (): void {
        $oldPassword = fake()->password(12, 20).'A1!';
        $newPassword = fake()->password(12, 20).'B2@';
        
        $user = User::factory()->create([
            'password' => Hash::make($oldPassword),
        ]);

        $this->actingAs($user);
        $action = new UpdateUserPassword();
        
        $action->update($user, [
            'current_password' => $oldPassword,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        expect(Hash::check($newPassword, $user->fresh()->password))->toBeTrue();
        expect($user->passwordResetHistories()->count())->toBe(1);
    });

    it('fails with incorrect current password', function (): void {
        $oldPassword = fake()->password(12, 20).'A1!';
        $wrongPassword = fake()->password(12, 20).'X9#';
        $newPassword = fake()->password(12, 20).'B2@';
        
        $user = User::factory()->create([
            'password' => Hash::make($oldPassword),
        ]);

        $this->actingAs($user);
        $action = new UpdateUserPassword();
        
        expect(fn() => $action->update($user, [
            'current_password' => $wrongPassword,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]))->toThrow(ValidationException::class);
    });

    it('does not validate password confirmation due to missing confirmed rule', function (): void {
        $oldPassword = fake()->password(12, 20).'A1!';
        $newPassword = fake()->password(12, 20).'B2@';
        $differentPassword = $newPassword.'Different'; // Ensure it's different
        
        $user = User::factory()->create([
            'password' => Hash::make($oldPassword),
        ]);

        $this->actingAs($user);
        $action = new UpdateUserPassword();
        
        // This should fail but doesn't because 'confirmed' rule is missing from PasswordValidationRules
        $action->update($user, [
            'current_password' => $oldPassword,
            'password' => $newPassword,
            'password_confirmation' => $differentPassword,
        ]);

        // Password gets updated even with wrong confirmation - this is a bug
        expect(Hash::check($newPassword, $user->fresh()->password))->toBeTrue();
    });

    it('fails with weak password', function (): void {
        $oldPassword = fake()->password(12, 20).'A1!';
        $user = User::factory()->create([
            'password' => Hash::make($oldPassword),
        ]);

        $this->actingAs($user);
        $action = new UpdateUserPassword();
        
        expect(fn() => $action->update($user, [
            'current_password' => $oldPassword,
            'password' => 'weak',
            'password_confirmation' => 'weak',
        ]))->toThrow(ValidationException::class);
    });
});

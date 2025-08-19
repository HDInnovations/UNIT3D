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

namespace App\Http\Livewire;

use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;
use Laravel\Fortify\Features;
use Livewire\Component;

class TwoFactorAuthForm extends Component
{
    /**
     * Indicates if two-factor authentication QR code is being displayed.
     */
    public bool $showingQrCode = false;

    /**
     * Indicates if the two-factor authentication confirmation input and button are being displayed.
     */
    public bool $showingConfirmation = false;

    /**
     * Indicates if two-factor authentication recovery codes are being displayed.
     */
    public bool $showingRecoveryCodes = false;

    /**
     * The OTP code for confirming two-factor authentication.
     */
    public string $code;

    /**
     * Mount the component.
     */
    final public function mount(): void
    {
        if (Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm') &&
            null === auth()->user()->two_factor_confirmed_at) {
            app(DisableTwoFactorAuthentication::class)(auth()->user());
        }
    }

    /**
     * Enable two-factor authentication for the user.
     */
    final public function enableTwoFactorAuthentication(EnableTwoFactorAuthentication $enable): void
    {
        $enable(auth()->user());

        $this->showingQrCode = true;

        if (Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm')) {
            $this->showingConfirmation = true;
        } else {
            $this->showingRecoveryCodes = true;
        }
    }

    /**
     * Confirm two-factor authentication for the user.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    final public function confirmTwoFactorAuthentication(ConfirmTwoFactorAuthentication $confirm): void
    {
        if (empty($this->code)) {
            $this->dispatch('error', type: 'error', message: 'The two factor authentication code input must not be empty.');

            return;
        }

        $confirm(auth()->user(), $this->code);

        $this->showingQrCode = false;
        $this->showingConfirmation = false;
        $this->showingRecoveryCodes = true;
    }

    /**
     * Display the user's recovery codes.
     */
    final public function showRecoveryCodes(): void
    {
        $this->showingRecoveryCodes = true;
    }

    /**
     * Generate new recovery codes for the user.
     */
    final public function regenerateRecoveryCodes(GenerateNewRecoveryCodes $generate): void
    {
        $generate(auth()->user());

        $this->showingRecoveryCodes = true;
    }

    /**
     * Disable two-factor authentication for the user.
     */
    final public function disableTwoFactorAuthentication(DisableTwoFactorAuthentication $disable): void
    {
        $disable(auth()->user());

        $this->showingQrCode = false;
        $this->showingConfirmation = false;
        $this->showingRecoveryCodes = false;
    }

    /**
     * Get the current user of the application.
     */
    final protected ?\Illuminate\Contracts\Auth\Authenticatable $user {
        get => auth()->user();
    }

    /**
     * Determine if two-factor authentication is enabled.
     */
    final protected bool $enabled {
        get => !empty($this->user->two_factor_secret);
    }

    /**
     * Render the component.
     */
    final public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.two-factor-auth-form');
    }
}

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

use Illuminate\Support\Facades\Log;
use LaravelWebauthn\Facades\Webauthn;
use LaravelWebauthn\Models\WebauthnKey;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Exception;

class SecurityKeyForm extends Component
{
    public bool $showRegisterForm = false;

    public string $keyName = '';

    public string $publicKeyOptions = '';

    final public function startRegistration(): void
    {
        $publicKey = Webauthn::prepareAttestation(auth()->user());

        $this->publicKeyOptions = json_encode($publicKey) ?: '';
        $this->showRegisterForm = true;
    }

    final public function cancelRegistration(): void
    {
        $this->showRegisterForm = false;
        $this->keyName = '';
        $this->publicKeyOptions = '';
    }

    /**
     * @param array<string, mixed> $credential
     */
    final public function registerKey(array $credential): void
    {
        $keyName = $this->keyName ?: 'Security Key '.now()->format('Y-m-d H:i');

        try {
            Webauthn::validateAttestation(auth()->user(), $credential, $keyName);

            $this->dispatch('success', type: 'success', message: 'Security key registered successfully!');
            $this->cancelRegistration();
        } catch (Exception $e) {
            Log::error('WebAuthn registration failed', [
                'error'      => $e->getMessage(),
                'credential' => $credential,
            ]);
            $this->dispatch('error', type: 'error', message: 'Failed to register security key: '.$e->getMessage());
        }
    }

    final public function deleteKey(int $keyId): void
    {
        $key = WebauthnKey::where('user_id', auth()->id())->find($keyId);

        if ($key === null) {
            $this->dispatch('error', type: 'error', message: 'Security key not found.');

            return;
        }

        $key->delete();

        $this->dispatch('success', type: 'success', message: 'Security key deleted successfully!');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, WebauthnKey>
     */
    #[Computed]
    final public function securityKeys(): \Illuminate\Database\Eloquent\Collection
    {
        return WebauthnKey::where('user_id', auth()->id())->orderByDesc('created_at')->get();
    }

    #[Computed]
    final public function hasSecurityKeys(): bool
    {
        return $this->securityKeys()->isNotEmpty();
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.security-key-form');
    }
}

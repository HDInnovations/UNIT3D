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

use Livewire\Component;
use ZxcvbnPhp\Zxcvbn;

class PasswordStrength extends Component
{
    public string $password = '';

    public string $passwordStrength = 'Weak';

    public int $strengthScore = 0;

    /**
     * @var array<int, string>
     */
    public array $strengthLevels = [
        1 => 'Weak',
        2 => 'Fair',
        3 => 'Good',
        4 => 'Strong',
    ];

    final public function updatedPassword(string $password): void
    {
        abort_if(\strlen($password) > 255, 422, 'Password too long');

        $this->strengthScore = (new Zxcvbn())->passwordStrength($password)['score'];
    }

    final public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.password-strength');
    }
}

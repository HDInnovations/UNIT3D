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

use App\Models\Thank;
use App\Models\Torrent;
use App\Models\User;
use App\Notifications\NewThank;
use Livewire\Component;

class ThankButton extends Component
{
    public ?Torrent $torrent = null;

    public ?User $user = null;

    final public function mount(): void
    {
        $this->user = auth()->user();
    }

    final public function store(): void
    {
        if ($this->user->id === $this->torrent->user_id) {
            $this->dispatch('error', type: 'error', message: 'You cannot thank your own content!');

            return;
        }

        if (Thank::query()->whereBelongsTo($this->user)->whereBelongsTo($this->torrent)->exists()) {
            $this->dispatch('error', type: 'error', message: 'You have already thanked!');

            return;
        }

        $thank = Thank::create([
            'user_id'    => $this->user->id,
            'torrent_id' => $this->torrent->id,
        ]);

        $uploader = $this->torrent->user;

        if ($uploader->acceptsNotification($this->user, $uploader, 'torrent', 'show_torrent_thank')) {
            $uploader->notify(new NewThank('torrent', $thank));
        }

        $this->dispatch('success', type: 'success', message: 'Your thank was successfully applied!');
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.thank-button');
    }
}

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

use App\Models\Torrent;
use App\Models\User;
use Livewire\Component;

class BookmarkButton extends Component
{
    public Torrent $torrent;

    public User $user;

    public bool $isBookmarked;

    public int $bookmarksCount;

    final public function store(): void
    {
        if ($this->user->bookmarks()->where('torrent_id', '=', $this->torrent->id)->exists()) {
            $this->dispatch('error', type: 'error', message: 'Torrent has already been bookmarked!');

            return;
        }

        $this->user->bookmarks()->attach($this->torrent->id);
        $this->isBookmarked = true;
        $this->bookmarksCount++;
        $this->dispatch('success', type: 'success', message: 'Torrent has been bookmarked successfully!');

        Torrent::query()->whereKey($this->torrent->id)->searchable();
    }

    final public function destroy(): void
    {
        $this->user->bookmarks()->detach($this->torrent->id);
        $this->isBookmarked = false;
        $this->bookmarksCount--;
        $this->dispatch('success', type: 'success', message: 'Torrent has been unbookmarked successfully!');

        Torrent::query()->whereKey($this->torrent->id)->searchable();
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.bookmark-button');
    }
}

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
 * @author     Roardom <roardom@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Notifications;

use App\Models\PlaylistSuggestion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PlaylistSuggestionRejected extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * PlaylistSuggestionRejected Constructor.
     */
    public function __construct(public PlaylistSuggestion $playlistSuggestion, public string $message)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Playlist suggestion rejected',
            'body'  => 'Playlist suggestion rejected. Note from playlist creator: '.$this->message,
            'url'   => '/playlists/'.$this->playlistSuggestion->playlist_id.'#playlist_suggestions',
        ];
    }
}

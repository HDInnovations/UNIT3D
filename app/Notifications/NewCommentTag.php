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

namespace App\Notifications;

use App\Models\Article;
use App\Models\Collection;
use App\Models\Comment;
use App\Models\Playlist;
use App\Models\Report;
use App\Models\Ticket;
use App\Models\Torrent;
use App\Models\TorrentRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewCommentTag extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * NewCommentTag Constructor.
     */
    public function __construct(public Torrent|TorrentRequest|Ticket|Playlist|Report|Collection|Article $model, public Comment $comment)
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
     * Determine if the notification should be sent.
     */
    public function shouldSend(User $notifiable): bool
    {
        // Do not notify self
        if ($this->comment->user_id === $notifiable->id) {
            return false;
        }

        // Enforce non-anonymous staff notifications to be sent
        if ($this->comment->user->group->is_modo &&
            ! $this->comment->anon) {
            return true;
        }

        // Evaluate general settings
        if ($notifiable->notification?->block_notifications === 1) {
            return false;
        }

        // Evaluate model based settings
        switch (true) {
            case $this->model instanceof Torrent:
                if ($notifiable->notification?->show_mention_torrent_comment === 0) {
                    return false;
                }

                // If the sender's group ID is found in the "Block all notifications from the selected groups" array,
                // the expression will return false.
                return ! \in_array($this->comment->user->group_id, $notifiable->notification?->json_mention_groups ?? [], true);
            case $this->model instanceof TorrentRequest:
                if ($notifiable->notification?->show_mention_request_comment === 0) {
                    return false;
                }

                // If the sender's group ID is found in the "Block all notifications from the selected groups" array,
                // the expression will return false.
                return ! \in_array($this->comment->user->group_id, $notifiable->notification?->json_mention_groups ?? [], true);
            case $this->model instanceof Ticket:
                return ! ($this->model->staff_id === $this->comment->id);
            case $this->model instanceof Playlist:
            case $this->model instanceof Article:
                if ($notifiable->notification?->show_mention_article_comment === 0) {
                    return false;
                }

                // If the sender's group ID is found in the "Block all notifications from the selected groups" array,
                // the expression will return false.
                return ! \in_array($this->comment->user->group_id, $notifiable->notification?->json_mention_groups ?? [], true);
            case $this->model instanceof Collection:
                break;
        }

        return true;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $username = $this->comment->anon ? 'Anonymous' : $this->comment->user->username;
        $title = $this->comment->anon ? 'You Have Been Tagged' : $username.' Has Tagged You';

        return match (true) {
            $this->model instanceof Torrent => [
                'title' => $title,
                'body'  => $username.' has tagged you in an comment on Torrent '.$this->model->name,
                'url'   => '/torrents/'.$this->model->id,
            ],
            $this->model instanceof TorrentRequest => [
                'title' => $title,
                'body'  => $username.' has tagged you in an comment on Torrent Request '.$this->model->name,
                'url'   => '/requests/'.$this->model->id,
            ],
            $this->model instanceof Ticket => [
                'title' => $title,
                'body'  => $username.' has tagged you in an comment on Ticket '.$this->model->subject,
                'url'   => '/tickets/'.$this->model->id,
            ],
            $this->model instanceof Report => [
                'title' => $title,
                'body'  => $username.' has tagged you in an comment on Report '.$this->model->title,
                'url'   => '/dashboard/reports/'.$this->model->id,
            ],
            $this->model instanceof Playlist => [
                'title' => $title,
                'body'  => $username.' has tagged you in an comment on Playlist '.$this->model->name,
                'url'   => '/playlists/'.$this->model->id,
            ],
            $this->model instanceof Collection => [
                'title' => $title,
                'body'  => $username.' has tagged you in an comment on Collection '.$this->model->name,
                'url'   => '/mediahub/collections/'.$this->model->id,
            ],
            $this->model instanceof Article => [
                'title' => $title,
                'body'  => $username.' has tagged you in an comment on Article '.$this->model->title,
                'url'   => '/articles/'.$this->model->id,
            ],
        };
    }
}

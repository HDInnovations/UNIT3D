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
use App\Models\TmdbCollection;
use App\Models\Comment;
use App\Models\Playlist;
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
    public function __construct(public Torrent|TorrentRequest|Ticket|Playlist|TmdbCollection|Article $model, public Comment $comment)
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
        switch ($this->model::class) {
            case Torrent::class:
                if ($notifiable->notification?->show_mention_torrent_comment === 0) {
                    return false;
                }

                // If the sender's group ID is found in the "Block all notifications from the selected groups" array,
                // the expression will return false.
                return ! \in_array($this->comment->user->group_id, $notifiable->notification?->json_mention_groups ?? [], true);
            case TorrentRequest::class:
                if ($notifiable->notification?->show_mention_request_comment === 0) {
                    return false;
                }

                // If the sender's group ID is found in the "Block all notifications from the selected groups" array,
                // the expression will return false.
                return ! \in_array($this->comment->user->group_id, $notifiable->notification?->json_mention_groups ?? [], true);
            case Ticket::class:
                return ! ($this->model->staff_id === $this->comment->id);
            case Playlist::class:
            case Article::class:
                if ($notifiable->notification?->show_mention_article_comment === 0) {
                    return false;
                }

                // If the sender's group ID is found in the "Block all notifications from the selected groups" array,
                // the expression will return false.
                return ! \in_array($this->comment->user->group_id, $notifiable->notification?->json_mention_groups ?? [], true);
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
        $username = $this->comment->anon ? __('application-messages.notification.anonymous-user') : $this->comment->user->username;
        $title = $this->comment->anon
            ? __('application-messages.notification.tagged-title-anonymous')
            : __('application-messages.notification.tagged-title', ['username' => $username]);

        return match ($this->model::class) {
            Torrent::class => [
                'title' => $title,
                'body'  => __('application-messages.notification.tagged-comment-torrent-body', ['username' => $username, 'name' => $this->model->name]),
                'url'   => '/torrents/'.$this->model->id,
            ],
            TorrentRequest::class => [
                'title' => $title,
                'body'  => __('application-messages.notification.tagged-comment-request-body', ['username' => $username, 'name' => $this->model->name]),
                'url'   => '/requests/'.$this->model->id,
            ],
            Ticket::class => [
                'title' => $title,
                'body'  => __('application-messages.notification.tagged-comment-ticket-body', ['username' => $username, 'subject' => $this->model->subject]),
                'url'   => '/tickets/'.$this->model->id,
            ],
            Playlist::class => [
                'title' => $title,
                'body'  => __('application-messages.notification.tagged-comment-playlist-body', ['username' => $username, 'name' => $this->model->name]),
                'url'   => '/playlists/'.$this->model->id,
            ],
            TmdbCollection::class => [
                'title' => $title,
                'body'  => __('application-messages.notification.tagged-comment-collection-body', ['username' => $username, 'name' => $this->model->name]),
                'url'   => '/mediahub/collections/'.$this->model->id,
            ],
            Article::class => [
                'title' => $title,
                'body'  => __('application-messages.notification.tagged-comment-article-body', ['username' => $username, 'title' => $this->model->title]),
                'url'   => '/articles/'.$this->model->id,
            ],
        };
    }
}

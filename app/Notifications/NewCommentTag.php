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
use App\Models\IgdbGame;
use App\Models\TmdbCollection;
use App\Models\TmdbMovie;
use App\Models\TmdbTv;
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
    public function __construct(public Torrent|TorrentRequest|Ticket|Playlist|TmdbCollection|TmdbMovie|TmdbTv|IgdbGame|Article $model, public Comment $comment)
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

        // Evaluate model-specific notification settings
        $modelSpecificDisabled = match ($this->model::class) {
            Torrent::class        => $notifiable->notification?->show_mention_torrent_comment === 0,
            TorrentRequest::class => $notifiable->notification?->show_mention_request_comment === 0,
            Ticket::class         => $this->model->staff_id === $this->comment->id,
            Playlist::class, Article::class => $notifiable->notification?->show_mention_article_comment === 0,
            default => false,
        };

        if ($modelSpecificDisabled) {
            return false;
        }

        // If the sender's group ID is in the user's blocked groups list, don't send
        return !\in_array($this->comment->user->group_id, $notifiable->notification?->json_mention_groups ?? [], true);
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

        return match ($this->model::class) {
            Torrent::class => [
                'title' => $title,
                'body'  => $username.' has tagged you in an comment on Torrent '.$this->model->name,
                'url'   => '/torrents/'.$this->model->id,
            ],
            TorrentRequest::class => [
                'title' => $title,
                'body'  => $username.' has tagged you in an comment on Torrent Request '.$this->model->name,
                'url'   => '/requests/'.$this->model->id,
            ],
            Ticket::class => [
                'title' => $title,
                'body'  => $username.' has tagged you in an comment on Ticket '.$this->model->subject,
                'url'   => '/tickets/'.$this->model->id,
            ],
            Playlist::class => [
                'title' => $title,
                'body'  => $username.' has tagged you in an comment on Playlist '.$this->model->name,
                'url'   => '/playlists/'.$this->model->id,
            ],
            TmdbCollection::class => [
                'title' => $title,
                'body'  => $username.' has tagged you in an comment on Collection '.$this->model->name,
                'url'   => '/mediahub/collections/'.$this->model->id,
            ],
            TmdbMovie::class => [
                'title' => $title,
                'body'  => $username.' has tagged you in a comment on Movie '.$this->model->title,
                'url'   => '/torrents/similar/'.($this->model->torrents()->value('category_id') ?? 1).'.'.$this->model->id,
            ],
            TmdbTv::class => [
                'title' => $title,
                'body'  => $username.' has tagged you in a comment on TV Show '.$this->model->name,
                'url'   => '/torrents/similar/'.($this->model->torrents()->value('category_id') ?? 2).'.'.$this->model->id,
            ],
            IgdbGame::class => [
                'title' => $title,
                'body'  => $username.' has tagged you in a comment on Game '.$this->model->name,
                'url'   => '/torrents/similar/'.Torrent::query()->where('igdb', '=', $this->model->id)->value('category_id').'.'.$this->model->id,
            ],
            Article::class => [
                'title' => $title,
                'body'  => $username.' has tagged you in an comment on Article '.$this->model->title,
                'url'   => '/articles/'.$this->model->id,
            ],
        };
    }
}

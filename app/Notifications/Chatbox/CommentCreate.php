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

namespace App\Notifications\Chatbox;

use App\Interfaces\ChatboxNotificationInterface;
use App\Models\Article;
use App\Models\Category;
use App\Models\Chatroom;
use App\Models\Comment;
use App\Models\IgdbGame;
use App\Models\Playlist;
use App\Models\TmdbCollection;
use App\Models\TmdbMovie;
use App\Models\TmdbTv;
use App\Models\Torrent;
use App\Models\TorrentRequest;
use App\Notifications\Channels\ChatboxNotificationChannel;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class CommentCreate extends Notification implements ShouldQueue, ChatboxNotificationInterface
{
    use Queueable;

    public function __construct(public Comment $comment, public ?Category $category)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return class-string
     */
    public function via(object $notifiable): string
    {
        return ChatboxNotificationChannel::class;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toChatboxNotification(Chatroom $notifiable): array
    {
        $this->comment->load('commentable');

        $username = $this->comment->anon ? 'An anonymous user' : '[url='.href_profile($this->comment->user)."]{$this->comment->user->username}[/url]";

        return [
            'message' => match ($this->comment->commentable::class) {
                Article::class        => "{$username} has left a comment on article [url=".href_article($this->comment->commentable)."]{$this->comment->commentable->title}[/url]",
                TmdbCollection::class => "{$username} has left a comment on collection [url=".href_collection($this->comment->commentable)."]{$this->comment->commentable->name}[/url]",
                Playlist::class       => "{$username} has left a comment on playlist [url=".href_playlist($this->comment->commentable)."]{$this->comment->commentable->name}[/url]",
                TorrentRequest::class => "{$username} has left a comment on torrent request [url=".href_request($this->comment->commentable)."]{$this->comment->commentable->name}[/url]",
                Torrent::class        => "{$username} has left a comment on torrent [url=".href_torrent($this->comment->commentable)."]{$this->comment->commentable->name}[/url]",
                TmdbMovie::class      => "{$username} has left a comment on torrent [url=".href_movie($this->comment->commentable, $this->category)."]{$this->comment->commentable->title}[/url]",
                TmdbTv::class         => "{$username} has left a comment on torrent [url=".href_tv($this->comment->commentable, $this->category)."]{$this->comment->commentable->name}[/url]",
                IgdbGame::class       => "{$username} has left a comment on torrent [url=".href_game($this->comment->commentable, $this->category)."]{$this->comment->commentable->name}[/url]",
                default               => throw new Exception('Unknown commentable type.'),
            },
        ];
    }
}

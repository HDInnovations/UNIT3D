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

use App\Achievements\UserMade100Comments;
use App\Achievements\UserMade200Comments;
use App\Achievements\UserMade300Comments;
use App\Achievements\UserMade400Comments;
use App\Achievements\UserMade500Comments;
use App\Achievements\UserMade50Comments;
use App\Achievements\UserMade600Comments;
use App\Achievements\UserMade700Comments;
use App\Achievements\UserMade800Comments;
use App\Achievements\UserMade900Comments;
use App\Achievements\UserMadeComment;
use App\Achievements\UserMadeTenComments;
use App\Enums\ModerationStatus;
use App\Models\Article;
use App\Models\Category;
use App\Models\Chatroom;
use App\Models\IgdbGame;
use App\Models\TmdbCollection;
use App\Models\TmdbMovie;
use App\Models\TmdbTv;
use App\Models\Playlist;
use App\Models\Ticket;
use App\Models\Torrent;
use App\Models\TorrentRequest;
use App\Models\User;
use App\Notifications\Chatbox\CommentCreate;
use App\Notifications\NewComment;
use App\Notifications\NewCommentTag;
use App\Traits\CastLivewireProperties;
use Illuminate\Support\Facades\Notification;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Comments extends Component
{
    use CastLivewireProperties;

    use WithPagination;

    public ?User $user;

    public null|Article|IgdbGame|Playlist|Ticket|TmdbCollection|TmdbMovie|TmdbTv|Torrent|TorrentRequest $model;

    public ?Category $category = null;

    public bool $anon = false;

    public int $perPage = 10;

    /**
     * @var array<string, string>
     */
    protected $listeners = [
        'refresh' => '$refresh',
    ];

    #[Validate('required')]
    public string $newCommentState = '';

    /**
     * @var array<string, string>
     */
    protected $validationAttributes = [
        'newCommentState' => 'comment',
    ];

    final public function mount(): void
    {
        $this->user = auth()->user();
    }

    final public function updating(string $field, mixed &$value): void
    {
        $this->castLivewireProperties($field, $value);
    }

    /**
     * @return array<string>
     */
    final public function taggedUsers(): array
    {
        preg_match_all('/@([\w\-]+)/', $this->newCommentState, $matches);

        return $matches[1];
    }

    final public function loadMore(): void
    {
        $this->perPage += 10;
    }

    /// TODO: Find a better data structure to avoid this mess of exception cases
    final public function postComment(): void
    {
        // Authorization
        abort_unless($this->model instanceof Ticket || ($this->user->can_comment ?? $this->user->group->can_comment), 403, __('comment.rights-revoked'));

        abort_if($this->model instanceof Torrent && $this->model->status !== ModerationStatus::APPROVED, 403, __('comment.torrent-status'));

        // Validation
        $this->validate();

        $comment = $this->model->comments()->create([
            'content' => $this->newCommentState,
            'user_id' => auth()->id(),
            'anon'    => $this->anon,
        ]);

        // New Comment Notification
        switch ($this->model::class) {
            case Ticket::class:
                // Notify assigned staff if needed
                if ($this->model->staff_id !== null) {
                    $staff = User::query()->with('group')->find($this->model->staff_id);

                    if ($staff !== null && $staff->group->is_modo) {
                        $staff->notify(new NewComment($this->model, $comment));
                    } else {
                        // Staff has been demoted since ticket was assigned, do not notify
                        $this->model->update(['staff_id' => null]);
                    }
                }

                // Notify ticket creator if needed
                User::query()->find($this->model->user_id)?->notify(new NewComment($this->model, $comment));

                break;
            case Article::class:
            case Playlist::class:
            case TorrentRequest::class:
            case Torrent::class:
                User::query()->find($this->model->user_id)?->notify(new NewComment($this->model, $comment));

                break;
        }

        // User Tagged Notification
        $users = User::query()->whereIn('username', $this->taggedUsers())->get();
        Notification::sendNow($users, new NewCommentTag($this->model, $comment));

        if (!$this->model instanceof Ticket) {
            // Auto Shout
            Chatroom::systemChatroom()->notify(new CommentCreate($comment, $this->category));

            // Achievements
            if (!$comment->anon) {
                $this->user->unlock(new UserMadeComment());
                $this->user->addProgress(new UserMadeTenComments(), 1);
                $this->user->addProgress(new UserMade50Comments(), 1);
                $this->user->addProgress(new UserMade100Comments(), 1);
                $this->user->addProgress(new UserMade200Comments(), 1);
                $this->user->addProgress(new UserMade300Comments(), 1);
                $this->user->addProgress(new UserMade400Comments(), 1);
                $this->user->addProgress(new UserMade500Comments(), 1);
                $this->user->addProgress(new UserMade600Comments(), 1);
                $this->user->addProgress(new UserMade700Comments(), 1);
                $this->user->addProgress(new UserMade800Comments(), 1);
                $this->user->addProgress(new UserMade900Comments(), 1);
            }
        }

        $this->reset('newCommentState');

        $this->gotoPage(1);
    }

    /**
     * @var \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, \App\Models\Comment>
     */
    final protected \Illuminate\Contracts\Pagination\LengthAwarePaginator $comments {
        get => $this->model
            ->comments()
            ->with(['user:id,username,group_id,image,title', 'user.group', 'children.user:id,username,group_id,image,title', 'children.user.group'])
            ->parent()
            ->latest()
            ->paginate($this->perPage);
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.comments', [
            'comments' => $this->comments,
        ]);
    }
}

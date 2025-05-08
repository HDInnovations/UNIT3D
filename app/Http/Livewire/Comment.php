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
use App\Models\TmdbCollection;
use App\Models\Playlist;
use App\Models\Ticket;
use App\Models\Torrent;
use App\Models\TorrentRequest;
use App\Models\User;
use App\Notifications\NewComment;
use App\Notifications\NewCommentTag;
use App\Repositories\ChatRepository;
use App\Traits\CastLivewireProperties;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

class Comment extends Component
{
    use CastLivewireProperties;

    protected ChatRepository $chatRepository;

    public null|Article|TmdbCollection|Playlist|Ticket|Torrent|TorrentRequest $model;

    public \App\Models\Comment $comment;

    public bool $anon = false;

    public ?User $user;

    /**
     * @var array<string, string>
     */
    protected $listeners = [
        'refresh' => '$refresh',
    ];

    public bool $isReplying = false;

    public string $replyState = '';

    public bool $isEditing = false;

    public string $editState = '';

    final public function boot(ChatRepository $chatRepository): void
    {
        $this->chatRepository = $chatRepository;
    }

    final public function mount(): void
    {
        $this->user = auth()->user();
    }

    final public function updating(string $field, mixed &$value): void
    {
        $this->castLivewireProperties($field, $value);
    }

    /**
     * @return list<string>
     */
    final public function taggedUsers(): array
    {
        preg_match_all('/@([\w\-]+)/', $this->editState, $matches);

        return $matches[1];
    }

    final public function updatedIsEditing(bool $isEditing): void
    {
        if (!$isEditing) {
            return;
        }

        $this->editState = $this->comment->content;
    }

    final public function editComment(): void
    {
        abort_unless(auth()->id() === $this->comment->user_id || auth()->user()->group->is_modo, 403);

        $this->validate([
            'editState' => 'required'
        ]);

        $this->comment->update([
            'content' => $this->editState,
        ]);

        $this->isEditing = false;
    }

    final public function deleteComment(): void
    {
        abort_unless((auth()->id() === $this->comment->user_id || auth()->user()->group->is_modo), 403, 'Permission Denied!');

        abort_if($this->comment->children()->exists(), 403);

        $this->comment->delete();
    }

    final public function postReply(): void
    {
        abort_unless($this->model instanceof Ticket || (auth()->user()->can_comment ?? auth()->user()->group->can_comment), 403, __('comment.rights-revoked'));

        abort_if($this->model instanceof Torrent && $this->model->status !== ModerationStatus::APPROVED, 403, __('comment.torrent-status'));

        abort_if($this->comment->isChild(), 403);

        $this->validate([
            'replyState' => 'required',
            'anon'       => 'bool',
        ]);

        $reply = $this->comment->children()->create([
            'content'          => $this->replyState,
            'user_id'          => auth()->id(),
            'anon'             => $this->anon,
            'commentable_id'   => $this->model->id,
            'commentable_type' => \get_class($this->model),
        ]);

        // New Comment Notification
        switch (true) {
            case $this->model instanceof Ticket:
                $ticket = $this->model;

                if ($this->user->id !== $ticket->staff_id && $ticket->staff_id !== null) {
                    User::find($ticket->staff_id)->notify(new NewComment($this->model, $reply));
                    $this->model->update(['staff_read' => false]);
                }

                if ($this->user->id !== $ticket->user_id) {
                    User::find($ticket->user_id)->notify(new NewComment($this->model, $reply));
                    $this->model->update(['user_read' => false]);
                }

                if (!\in_array($this->comment->user_id, [$ticket->staff_id, $ticket->user_id, $this->user->id])) {
                    User::find($this->comment->user_id)->notify(new NewComment($this->model, $reply));
                }

                break;
            case $this->model instanceof Article:
            case $this->model instanceof Playlist:
            case $this->model instanceof TorrentRequest:
            case $this->model instanceof Torrent:
                if ($this->user->id !== $this->model->user_id) {
                    User::find($this->model->user_id)?->notify(new NewComment($this->model, $reply));
                }

                if ($this->user->id !== $this->comment->user_id && $this->comment->user_id !== $this->model->user_id) {
                    User::find($this->comment->user_id)?->notify(new NewComment($this->model, $reply));
                }

                break;
        }

        // User Tagged Notification
        $users = User::whereIn('username', $this->taggedUsers())->get();
        Notification::sendNow($users, new NewCommentTag($this->model, $reply));

        if (!$this->model instanceof Ticket) {
            // Auto Shout
            $username = $reply->anon ? 'An anonymous user' : '[url='.href_profile($this->user).']'.$this->user->username.'[/url]';

            switch (true) {
                case $this->model instanceof Article:
                    $this->chatRepository->systemMessage($username.' has left a comment on Article [url='.href_article($this->model).']'.$this->model->title.'[/url]');

                    break;
                case $this->model instanceof TmdbCollection:
                    $this->chatRepository->systemMessage($username.' has left a comment on Collection [url='.href_collection($this->model).']'.$this->model->name.'[/url]');

                    break;
                case $this->model instanceof Playlist:
                    $this->chatRepository->systemMessage($username.' has left a comment on Playlist [url='.href_playlist($this->model).']'.$this->model->name.'[/url]');

                    break;
                case $this->model instanceof TorrentRequest:
                    $this->chatRepository->systemMessage($username.' has left a comment on Torrent Request [url='.href_request($this->model).']'.$this->model->name.'[/url]');

                    break;
                case $this->model instanceof Torrent:
                    $this->chatRepository->systemMessage($username.' has left a comment on Torrent [url='.href_torrent($this->model).']'.$this->model->name.'[/url]');

                    break;
            }

            // Achievements
            if (!$reply->anon) {
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

        $this->reset('replyState', 'isReplying');

        $this->dispatch('refresh')->self();
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.comment');
    }
}

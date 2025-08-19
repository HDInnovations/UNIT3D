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
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Comments extends Component
{
    use CastLivewireProperties;

    use WithPagination;

    protected ChatRepository $chatRepository;

    public ?User $user;

    public null|Article|TmdbCollection|Playlist|Ticket|Torrent|TorrentRequest $model;

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
        switch (true) {
            case $this->model instanceof Ticket:
                // Notify assigned staff if needed
                User::find($this->model->staff_id)?->notify(new NewComment($this->model, $comment));

                // Notify ticket creator if needed
                User::find($this->model->user_id)?->notify(new NewComment($this->model, $comment));

                break;
            case $this->model instanceof Article:
            case $this->model instanceof Playlist:
            case $this->model instanceof TorrentRequest:
            case $this->model instanceof Torrent:
                User::find($this->model->user_id)?->notify(new NewComment($this->model, $comment));

                break;
        }

        // User Tagged Notification
        $users = User::whereIn('username', $this->taggedUsers())->get();
        Notification::sendNow($users, new NewCommentTag($this->model, $comment));

        if (!$this->model instanceof Ticket) {
            // Auto Shout
            $username = $comment->anon ? 'An anonymous user' : '[url='.href_profile($this->user).']'.$this->user->username.'[/url]';

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

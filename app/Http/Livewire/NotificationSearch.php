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

use App\Models\User;
use App\Traits\LivewireSort;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class NotificationSearch extends Component
{
    use LivewireSort;
    use WithPagination;

    #TODO: Update URL attributes once Livewire 3 fixes upstream bug. See: https://github.com/livewire/livewire/discussions/7746

    #[Url(history: true)]
    public bool $bon_gifts = false;

    #[Url(history: true)]
    public bool $comment = false;

    #[Url(history: true)]
    public bool $comment_tags = false;

    #[Url(history: true)]
    public bool $followers = false;

    #[Url(history: true)]
    public bool $playlist_suggestions = false;

    #[Url(history: true)]
    public bool $playlist_suggestion_rejections = false;

    #[Url(history: true)]
    public bool $posts = false;

    #[Url(history: true)]
    public bool $post_tags = false;

    #[Url(history: true)]
    public bool $post_tips = false;

    #[Url(history: true)]
    public bool $request_bounties = false;

    #[Url(history: true)]
    public bool $request_claims = false;

    #[Url(history: true)]
    public bool $request_fills = false;

    #[Url(history: true)]
    public bool $request_approvals = false;

    #[Url(history: true)]
    public bool $request_rejections = false;

    #[Url(history: true)]
    public bool $request_unclaims = false;

    #[Url(history: true)]
    public bool $reseed_requests = false;

    #[Url(history: true)]
    public bool $thanks = false;

    #[Url(history: true)]
    public bool $upload_tips = false;

    #[Url(history: true)]
    public bool $topics = false;

    #[Url(history: true)]
    public bool $unfollows = false;

    #[Url(history: true)]
    public bool $uploads = false;

    #[Url(history: true)]
    public int $perPage = 25;

    #[Url(history: true)]
    public string $sortField = 'created_at';

    #[Url(history: true)]
    public string $sortDirection = 'desc';

    /**
     * @var \Illuminate\Pagination\LengthAwarePaginator<int, \Illuminate\Notifications\DatabaseNotification>
     */
    final protected \Illuminate\Pagination\LengthAwarePaginator $notifications {
        get => auth()->user()->notifications()
            ->select('*')
            ->selectRaw("CASE WHEN read_at IS NULL THEN 'FALSE' ELSE 'TRUE' END as is_read")
            ->where(function ($query): void {
                $query->when($this->bon_gifts, function ($query): void {
                    $query->orWhere('type', '=', \App\Notifications\NewBon::class);
                })
                    ->when($this->comment, function ($query): void {
                        $query->orWhere('type', '=', \App\Notifications\NewComment::class);
                    })
                    ->when($this->comment_tags, function ($query): void {
                        $query->orWhere('type', '=', \App\Notifications\NewCommentTag::class);
                    })
                    ->when($this->followers, function ($query): void {
                        $query->orWhere('type', '=', \App\Notifications\NewFollow::class);
                    })
                    ->when($this->playlist_suggestions, function ($query): void {
                        $query->orWhere('type', '=', \App\Notifications\PlaylistSuggestionCreated::class);
                    })
                    ->when($this->playlist_suggestion_rejections, function ($query): void {
                        $query->orWhere('type', '=', \App\Notifications\PlaylistSuggestionRejected::class);
                    })
                    ->when($this->posts, function ($query): void {
                        $query->orWhere('type', '=', \App\Notifications\NewPost::class);
                    })
                    ->when($this->post_tags, function ($query): void {
                        $query->orWhere('type', '=', \App\Notifications\NewPostTag::class);
                    })
                    ->when($this->post_tips, function ($query): void {
                        $query->orWhere('type', '=', \App\Notifications\NewPostTip::class);
                    })
                    ->when($this->request_bounties, function ($query): void {
                        $query->orWhere('type', '=', \App\Notifications\NewRequestBounty::class);
                    })
                    ->when($this->request_claims, function ($query): void {
                        $query->orWhere('type', '=', \App\Notifications\NewRequestClaim::class);
                    })
                    ->when($this->request_fills, function ($query): void {
                        $query->orWhere('type', '=', \App\Notifications\NewRequestFill::class);
                    })
                    ->when($this->request_approvals, function ($query): void {
                        $query->orWhere('type', '=', \App\Notifications\NewRequestFillApprove::class);
                    })
                    ->when($this->request_rejections, function ($query): void {
                        $query->orWhere('type', '=', \App\Notifications\NewRequestFillReject::class);
                    })
                    ->when($this->request_unclaims, function ($query): void {
                        $query->orWhere('type', '=', \App\Notifications\NewRequestUnclaim::class);
                    })
                    ->when($this->reseed_requests, function ($query): void {
                        $query->orWhere('type', '=', \App\Notifications\NewReseedRequest::class);
                    })
                    ->when($this->thanks, function ($query): void {
                        $query->orWhere('type', '=', \App\Notifications\NewThank::class);
                    })
                    ->when($this->upload_tips, function ($query): void {
                        $query->orWhere('type', '=', \App\Notifications\NewUploadTip::class);
                    })
                    ->when($this->topics, function ($query): void {
                        $query->orWhere('type', '=', \App\Notifications\NewTopic::class);
                    })
                    ->when($this->unfollows, function ($query): void {
                        $query->orWhere('type', '=', \App\Notifications\NewUnfollow::class);
                    })
                    ->when($this->uploads, function ($query): void {
                        $query->orWhere('type', '=', \App\Notifications\NewUpload::class);
                    });
            })
            ->reorder()
            ->orderBy('is_read')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.notification-search', [
            'user'          => User::with(['group'])->findOrFail(auth()->id()),
            'notifications' => $this->notifications,
        ]);
    }
}

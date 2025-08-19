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

namespace App\Http\Livewire;

use App\Models\ForumCategory;
use App\Models\Topic;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class TopicSearch extends Component
{
    use WithPagination;

    #TODO: Update URL attributes once Livewire 3 fixes upstream bug. See: https://github.com/livewire/livewire/discussions/7746

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $sortField = 'last_post_created_at';

    #[Url(history: true)]
    public string $sortDirection = 'desc';

    #[Url(history: true)]
    public string $label = '';

    #[Url(history: true)]
    public string $state = '';

    #[Url(history: true)]
    public string $subscribed = '';

    #[Url(history: true)]
    public string $forumId = '';

    #[Url(history: true)]
    public string $read = '';

    final public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * @var \Illuminate\Database\Eloquent\Collection<int, ForumCategory>
     */
    final protected \Illuminate\Database\Eloquent\Collection $forumCategories {
        get => ForumCategory::query()
            ->with([
                'forums' => fn ($query) => $query->authorized(canReadTopic: true)
            ])
            ->orderBy('position')
            ->get()
            ->filter(fn ($category) => $category->forums->isNotEmpty());
    }

    /**
     * @var \Illuminate\Pagination\LengthAwarePaginator<int, Topic>
     */
    final protected \Illuminate\Pagination\LengthAwarePaginator $topics {
        get => Topic::query()
            ->select('topics.*')
            ->with([
                'user.group',
                'latestPoster',
                'forum',
                'reads' => fn ($query) => $query->whereBelongsTo(auth()->user()),
            ])
            ->authorized(canReadTopic: true)
            ->when($this->search !== '', fn ($query) => $query->where('name', 'LIKE', '%'.$this->search.'%'))
            ->when($this->label !== '', fn ($query) => $query->where($this->label, '=', 1))
            ->when($this->state !== '', fn ($query) => $query->where('state', '=', $this->state))
            ->when(
                $this->subscribed === 'include',
                fn ($query) => $query
                    ->whereRelation('subscribedUsers', 'users.id', '=', auth()->id())
            )
            ->when(
                $this->subscribed === 'exclude',
                fn ($query) => $query
                    ->whereDoesntHave('subscribedUsers', fn ($query) => $query->where('users.id', '=', auth()->id()))
            )
            ->when(
                $this->read === 'some',
                fn ($query) => $query
                    ->whereHas(
                        'reads',
                        fn ($query) => $query
                            ->whereBelongsTo(auth()->user())
                            ->whereColumn('last_post_id', '>', 'last_read_post_id')
                    )
            )
            ->when(
                $this->read === 'all',
                fn ($query) => $query
                    ->whereHas(
                        'reads',
                        fn ($query) => $query
                            ->whereBelongsTo(auth()->user())
                            ->whereColumn('last_post_id', '=', 'last_read_post_id')
                    )
            )
            ->when(
                $this->read === 'none',
                fn ($query) => $query
                    ->whereDoesntHave('reads', fn ($query) => $query->whereBelongsTo(auth()->user()))
            )
            ->when($this->forumId !== '', fn ($query) => $query->where('forum_id', '=', $this->forumId))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(25);
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.topic-search', [
            'topics'          => $this->topics,
            'forumCategories' => $this->forumCategories,
        ]);
    }
}

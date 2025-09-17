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

use App\Models\Post;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class PostSearch extends Component
{
    use WithPagination;

    #TODO: Update URL attributes once Livewire 3 fixes upstream bug. See: https://github.com/livewire/livewire/discussions/7746

    #[Url(history: true)]
    public String $search = '';

    final public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * @var \Illuminate\Pagination\LengthAwarePaginator<int, Post>
     */
    final protected \Illuminate\Pagination\LengthAwarePaginator $posts {
        get => Post::query()
            ->with('user.group', 'topic:id,name,state')
            ->withCount('likes', 'dislikes', 'authorPosts', 'authorTopics')
            ->withSum('tips', 'bon')
            ->withExists([
                'likes'    => fn ($query) => $query->where('user_id', '=', auth()->id()),
                'dislikes' => fn ($query) => $query->where('user_id', '=', auth()->id()),
            ])
            ->authorized(canReadTopic: true)
            ->when($this->search !== '', fn ($query) => $query->where('content', 'LIKE', '%'.$this->search.'%'))
            ->orderByDesc('created_at')
            ->paginate(25);
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.post-search', [
            'posts' => $this->posts,
        ]);
    }
}

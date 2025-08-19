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

use App\Models\Conversation;
use App\Traits\LivewireSort;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ConversationSearch extends Component
{
    use LivewireSort;
    use WithPagination;

    #[Url(history: true)]
    public string $tab = 'inbox';

    #[Url(history: true, except: '')]
    public ?string $subject = null;

    #[Url(history: true, except: '')]
    public ?string $username = null;

    #[Url(history: true, except: '')]
    public ?string $message = null;

    #[Url(history: true)]
    #[Rule('in:subject,created_at,updated_at,messages_count')]
    public string $sortField = 'updated_at';

    #[Url(history: true)]
    public string $sortDirection = 'desc';

    /**
     * @var \Illuminate\Pagination\LengthAwarePaginator<int, Conversation>
     */
    final protected \Illuminate\Pagination\LengthAwarePaginator $conversations {
        get => Conversation::query()
            ->with([
                'users'        => fn ($query) => $query->with('group')->where('users.id', '!=', auth()->id()),
                'participants' => fn ($query) => $query->where('user_id', auth()->id()),
            ])
            ->withCount('messages')
            ->whereHas(
                'participants',
                fn ($query) => $query
                    ->where('user_id', '=', auth()->id())
                    ->when($this->tab === 'unread', fn ($query) => $query->where('read', '=', false))
            )
            ->when(
                $this->subject !== null && $this->subject !== '',
                fn ($query) => $query->where('subject', 'LIKE', '%'.str_replace(' ', '%', $this->subject).'%')
            )
            ->when(
                $this->username !== null && $this->username !== '',
                fn ($query) => $query
                    ->whereHas(
                        'messages',
                        fn ($query) => $query->whereRelation('sender', 'username', 'LIKE', $this->username)
                    )
            )
            ->when(
                $this->message !== null && $this->message !== '',
                fn ($query) => $query->whereRelation('messages', 'message', 'LIKE', '%'.str_replace(' ', '%', $this->message).'%')
            )
            ->when(
                $this->tab === 'inbox' || $this->tab === 'unread',
                fn ($query) => $query->whereRelation('messages', 'sender_id', '!=', auth()->id())
            )
            ->when(
                $this->tab === 'outbox',
                fn ($query) => $query->whereRelation('messages', 'sender_id', '=', auth()->id())
            )
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(25);
    }

    /**
     * Delete A Conversation.
     */
    public function destroy(Conversation $conversation): void
    {
        $conversation->participants()->whereBelongsTo(auth()->user())->delete();

        $this->dispatch('success', type: 'success', message: 'Conversation deleted');
    }

    /**
     * Delete A Conversation.
     */
    public function markRead(Conversation $conversation): void
    {
        $conversation->participants()->whereBelongsTo(auth()->user())->update([
            'read' => true,
        ]);

        $this->dispatch('success', type: 'success', message: 'Conversation marked read');
    }

    /**
     * Delete A Conversation.
     */
    public function markUnread(Conversation $conversation): void
    {
        $conversation->participants()->whereBelongsTo(auth()->user())->update([
            'read' => false,
        ]);

        $this->dispatch('success', type: 'success', message: 'Conversation marked unread');
    }

    public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.conversation-search', [
            'conversations' => $this->conversations,
            'user'          => auth()->user(),
        ]);
    }
}

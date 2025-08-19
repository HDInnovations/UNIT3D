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

use App\Models\Note;
use App\Models\User;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class UserNotes extends Component
{
    use WithPagination;

    public User $user;

    #TODO: Update URL attributes once Livewire 3 fixes upstream bug. See: https://github.com/livewire/livewire/discussions/7746

    public string $message = '';

    /**
     * @var array<int, string>
     */
    public array $messages = [];

    #[Url(history: true)]
    public int $perPage = 25;

    #[Url(history: true)]
    public string $sortField = 'created_at';

    #[Url(history: true)]
    public string $sortDirection = 'desc';

    /**
     * @var array<mixed>
     */
    protected array $rules = [
        'message' => [
            'required',
            'filled',
        ],
        'messages' => [
            'array',
        ],
        'messages.*' => [
            'required',
            'filled',
        ]
    ];

    final public function mount(): void
    {
        $this->messages = Note::where('user_id', '=', $this->user->id)->pluck('message', 'id')->toArray();
    }

    /**
     * @var \Illuminate\Pagination\LengthAwarePaginator<int, Note>
     */
    final protected \Illuminate\Pagination\LengthAwarePaginator $notes {
        get => Note::query()
            ->with('staffuser', 'staffuser.group')
            ->where('user_id', '=', $this->user->id)
            ->paginate($this->perPage);
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.user-notes', [
            'notes' => $this->notes,
        ]);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    final public function store(): void
    {
        abort_unless(auth()->user()->group->is_modo, 403);

        $this->validateOnly('message');

        Note::create([
            'user_id'  => $this->user->id,
            'staff_id' => auth()->id(),
            'message'  => $this->message,
        ]);

        $this->message = '';

        $this->dispatch('success', type: 'success', message: 'Note has successfully been posted!');
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    final public function update(int $id): void
    {
        abort_unless(auth()->user()->group->is_modo, 403);

        $this->validateOnly('messages');
        $this->validateOnly('messages.*');

        Note::whereKey($id)->update([
            'staff_id'   => auth()->id(),
            'message'    => $this->messages[$id],
            'updated_at' => now(),
        ]);

        $this->dispatch('success', type: 'success', message: 'Note has successfully been updated!');
    }

    final public function destroy(int $id): void
    {
        abort_unless(auth()->user()->group->is_modo, 403);

        Note::findOrFail($id)->delete();

        $this->dispatch('success', type: 'success', message: 'Note has successfully been deleted!');
    }
}

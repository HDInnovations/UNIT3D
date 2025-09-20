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

use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class AttachmentUpload extends Component
{
    use WithFileUploads;

    public ?User $user = null;

    public ?int $ticket = null;

    #[Validate('image|max:1024')]
    public $attachment;

    public $storedImage;

    final public function mount(int $id): void
    {
        $this->user = auth()->user();
        $this->ticket = $id;
    }

    final public function updatedAttachment(): void
    {
        $this->validate();

        $ticket = Ticket::find($this->ticket);

        abort_unless($ticket->user_id === $this->user->id || $this->user->group->is_modo, 403);

        $fileName = uniqid('', true).'.'.$this->attachment->getClientOriginalExtension();

        $this->attachment->storeAs('', $fileName, 'attachment-files');

        $attachment = new TicketAttachment();
        $attachment->user_id = $this->user->id;
        $attachment->ticket_id = $ticket->id;
        $attachment->file_name = $fileName;
        $attachment->file_size = $this->attachment->getSize();
        $attachment->file_extension = $this->attachment->getMimeType();
        $attachment->save();

        $this->dispatch('success', type: 'success', message: 'Ticket attachment uploaded successfully!');
    }

    /**
     * @var \Illuminate\Database\Eloquent\Collection<int, TicketAttachment>
     */
    protected \Illuminate\Database\Eloquent\Collection $attachments {
        get {
            $ticket = Ticket::find($this->ticket);

            abort_unless($ticket->user_id === $this->user->id || $this->user->group->is_modo, 403);

            return $ticket->attachments;
        }
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.attachment-upload', ['attachments' => $this->attachments]);
    }
}

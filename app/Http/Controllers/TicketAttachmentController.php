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

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketAttachmentController extends Controller
{
    /**
     * Download a ticket attachment from storage.
     */
    final public function download(Request $request, Ticket $ticket, TicketAttachment $attachment): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        abort_unless($request->user()->group->is_modo || $request->user()->id === $ticket->user_id, 403);

        abort_unless($attachment->ticket_id === $ticket->id, 404);

        return response()->download(Storage::disk('attachment-files')->path($attachment->file_name))->deleteFileAfterSend(false);
    }

    /**
     * Stores one or multiple ticket attachments from the ticket create form.
     */
    public static function storeTicketAttachments(Request $request, Ticket $ticket, User $user): void
    {
        foreach ($request->file('attachments') as $file) {
            if ($file->getSize() > 1000000) {
                continue;
            }

            $allowedMimes = ['image/jpeg', 'image/jpg', 'image/bmp', 'image/gif', 'image/png', 'image/webp'];

            if (! \in_array($file->getMimeType(), $allowedMimes)) {
                continue;
            }

            $fileName = uniqid('', true).'.'.$file->getClientOriginalExtension();
            $file->storeAs('', $fileName, 'attachment-files');

            $attachment = new TicketAttachment();
            $attachment->user_id = $user->id;
            $attachment->ticket_id = $ticket->id;
            $attachment->file_name = $fileName;
            $attachment->file_size = $file->getSize();
            $attachment->file_extension = $file->getMimeType();
            $attachment->save();
        }
    }
}

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

namespace App\Jobs;

use App\Models\Conversation;
use App\Models\PrivateMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessMassPM implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * ProcessMassPM Constructor.
     */
    public function __construct(protected int $senderId, protected int $receiverId, protected string $subject, protected string $message)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $conversation = Conversation::query()->create(['subject' => $this->subject]);

        $conversation->users()->sync([$this->senderId => ['read' => true], $this->receiverId]);

        PrivateMessage::query()->create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $this->senderId,
            'message'         => $this->message
        ]);
    }
}

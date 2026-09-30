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

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DisableUser extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * DisableUser Constructor.
     */
    public function __construct(public string $email)
    {
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this->markdown('emails.disabled')
            ->subject(__('application-messages.mail.disable-user-subject', ['site' => config('other.title')]));
    }
}

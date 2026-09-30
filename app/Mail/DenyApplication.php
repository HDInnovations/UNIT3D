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

class DenyApplication extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * DenyApplication Constructor.
     */
    public function __construct(public string $deniedMessage)
    {
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this->markdown('emails.deny-application')->subject(__('application-messages.mail.deny-application-subject'));
    }
}

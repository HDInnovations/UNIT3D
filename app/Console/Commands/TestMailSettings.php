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

namespace App\Console\Commands;

use App\Mail\TestEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Exception;
use Throwable;

class TestMailSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email to owner account using the current mail configuration';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        $owner = config('other.email');

        $this->info('Sending test email to '.$owner);

        if ($this->option('force') || $this->confirm('Do you wish to continue?', true)) {
            try {
                Mail::to($owner)->send(new TestEmail());
            } catch (Exception) {
                $this->error('Failed!');
                $this->alert('Email failed to send. Please review your mail configs in the .env file.');

                exit(1);
            }

            $this->alert('Email was successfully sent!');
        }
    }
}

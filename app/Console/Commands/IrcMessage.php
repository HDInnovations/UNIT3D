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

use App\Bots\IRCAnnounceBot;
use Exception;
use Illuminate\Console\Command;
use Override;
use Symfony\Component\Console\Input\InputArgument;
use Throwable;

class IrcMessage extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'irc:message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Messages an IRC Channel';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        $this->info('Messaging '.$this->argument('channel').': '.$this->argument('message'));

        (new IRCAnnounceBot())
            ->to($this->argument('channel'))
            ->say($this->argument('message'));
    }

    /**
     * Get the console command arguments.
     *
     * @return array<int, array<int, int|string>>
     */
    #[Override]
    protected function getArguments(): array
    {
        return [
            ['channel', InputArgument::REQUIRED, 'Channel that you would like to message'],
            ['message', InputArgument::REQUIRED, 'Message you would like to send'],
        ];
    }
}

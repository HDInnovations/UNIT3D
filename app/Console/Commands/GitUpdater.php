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

use App\Console\ConsoleTools;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use RuntimeException;
use Throwable;

class GitUpdater extends Command
{
    use ConsoleTools;

    /**
     * The copy command.
     */
    private string $copyCommand = 'cp -Rfp';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'git:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executes The Commands Necessary To Update Your Website Using Git';

    /**
     * @var array<string>
     */
    private const array ADDITIONAL = [
        '.env',
        'laravel-echo-server.json',
    ];

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        $this->input = new ArgvInput();
        $this->output = new ConsoleOutput();

        $this->io = new SymfonyStyle($this->input, $this->output);

        $this->info('
        ***************************
        * Git Updater v3.0   *
        ***************************
        ');

        $this->line('<fg=cyan>
        THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"

        IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
        SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
        GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) EVEN IF ADVISED OF THE POSSIBILITY
        OF SUCH DAMAGE.

        WITH THAT SAID YOU CAN BE GUARANTEED THAT YOUR DATABASE WILL NOT BE ALTERED.

        <fg=red>BY PROCEEDING YOU AGREE TO THE ABOVE DISCLAIMER! USE AT YOUR OWN RISK!</>
        </>');

        if (!$this->io->confirm('Would you like to proceed', true)) {
            $this->line('<fg=red>Aborted ...</>');

            exit();
        }

        $this->io->writeln('
        Press CTRL + C ANYTIME to abort! Aborting can lead to unexpected results!
        ');

        sleep(1);

        $this->update();

        $this->white('Please report any errors or issues.');

        $this->done();
    }

    private function update(): void
    {
        $updating = $this->checkForUpdates();

        if ((is_countable($updating) ? \count($updating) : 0) > 0) {
            $this->alertDanger('Found Updates');

            $this->cyan('Files that need updated:');
            $this->io->listing($updating);

            if ($this->io->confirm('Start the update process', true)) {
                $this->call('down');

                $this->process('git add .');

                $paths = $this->paths();

                $this->backup($paths);

                $this->header('Resetting Repository');

                $this->commands([
                    'git fetch origin',
                    'git reset --hard origin/master',
                ]);

                $this->restore($paths);

                $conflicts = array_intersect($updating, $paths);

                if ($conflicts !== []) {
                    $this->red('There are some files that was not updated because because of conflicts.');
                    $this->red('We will walk you through updating these files now.');

                    $this->manualUpdate($conflicts);
                }

                if ($this->io->confirm('Run new migrations (php artisan migrate)', true)) {
                    $this->migrations();
                }

                $this->clearCache();

                if ($this->io->confirm('Install new packages (composer install)', true)) {
                    $this->composer();
                }

                $this->updateUNIT3DConfig();

                $this->setCache();

                if ($this->io->confirm('Compile assets (bun run build)', true)) {
                    $this->compile();
                }

                $this->permissions();

                $this->supervisor();

                $this->php();

                $this->header('Bringing Site Live');
                $this->call('up');
            } else {
                $this->alertDanger('Aborted Update');

                exit();
            }
        } else {
            $this->alertSuccess('No Available Updates Found');
        }
    }

    /**
     * Check for updates.
     *
     * @return array<string>
     */
    private function checkForUpdates(): array
    {
        $this->header('Checking For Updates');

        $this->process('git fetch origin');
        $process = $this->process('git diff ..origin/master --name-only');
        $updating = array_filter(explode("\n", $process->getOutput()), 'strlen');

        $this->done();

        return $updating;
    }

    /*
     * Manually update files that have conflicts.
     *
     * @param array<string> $updating
     */
    private function manualUpdate(array $updating): void
    {
        $this->alertInfo('Manual Update');
        $this->red('Updating will cause you to LOSE any changes you might have made to the file!');

        foreach ($updating as $file) {
            if ($this->io->confirm(\sprintf('Update %s', $file), true)) {
                $this->updateFile($file);
            }
        }

        $this->done();
    }

    /**
     * Update a file.
     */
    private function updateFile(string $file): void
    {
        $this->process(\sprintf('git checkout origin/master -- %s', $file));
    }

    /**
     * Backup the files that will be updated.
     *
     * @param array<string> $paths
     */
    private function backup(array $paths): void
    {
        $this->header('Backing Up Files');

        $this->commands([
            'rm -rf '.storage_path('gitupdate'),
            'mkdir '.storage_path('gitupdate'),
        ], true);

        foreach ($paths as $path) {
            $this->validatePath($path);
            $this->createBackupPath($path);
            $this->process($this->copyCommand.' '.base_path($path).' '.storage_path('gitupdate').'/'.$path);
        }

        $this->done();
    }

    /**
     * Restore the files that were backed up.
     *
     * @param array<string> $paths
     */
    private function restore(array $paths): void
    {
        $this->header('Restoring Backups');

        foreach ($paths as $path) {
            $to = Str::replaceLast('/.', '', base_path(\dirname((string) $path)));
            $from = storage_path('gitupdate').'/'.$path;

            if (is_dir($from)) {
                $to .= '/'.basename($from).'/';
                $from .= '/*';
            }

            $this->process(\sprintf('%s %s %s', $this->copyCommand, $from, $to));
        }

        $this->commands([
            'git add .',
            'git checkout origin/master -- bun.lockb',
            'git checkout origin/master -- composer.lock',
        ]);
    }

    private function composer(): void
    {
        $this->header('Installing Composer Packages');

        $this->commands([
            'composer self-update',
            'composer install --prefer-dist --no-dev -o',
        ]);

        $this->done();
    }

    private function compile(): void
    {
        $this->header('Compiling Assets ...');

        $this->commands([
            'bun install',
            'bun run build',
        ]);

        $this->done();
    }

    private function updateUNIT3DConfig(): void
    {
        $this->header('Updating UNIT3D Configuration File');
        $this->process('git fetch origin && git checkout origin/master -- config/unit3d.php');
        $this->done();
    }

    private function clearCache(): void
    {
        $this->header('Clearing Application Cache');
        $this->call('clear:all_cache');
        $this->done();
    }

    private function setCache(): void
    {
        $this->header('Setting Cache');
        $this->call('set:all_cache');
        $this->done();
    }

    private function migrations(): void
    {
        $this->header('Running New Migrations');
        $this->call('migrate');
        $this->done();
    }

    private function permissions(): void
    {
        $this->header('Refreshing Permissions');
        $this->process('chown -R www-data: storage bootstrap public config');
        $this->done();
    }

    private function supervisor(): void
    {
        $this->header('Restarting Supervisor');
        $this->call('queue:restart');
        $this->process('supervisorctl reread && supervisorctl update && supervisorctl reload');
        $this->done();
    }

    private function php(): void
    {
        $this->header('Restarting PHP');
        $this->process('systemctl restart php8.4-fpm');
        $this->done();
    }

    private function validatePath(string $path): void
    {
        if (!is_file(base_path($path)) && !is_dir(base_path($path))) {
            $this->red(\sprintf("The path '%s' is invalid", $path));
        }
    }

    private function createBackupPath(string $path): void
    {
        if (!is_dir(storage_path(\sprintf('gitupdate/%s', $path))) && !is_file(base_path($path))) {
            if (!mkdir($concurrentDirectory = storage_path(\sprintf('gitupdate/%s', $path)), 0775, true) && !is_dir($concurrentDirectory)) {
                throw new RuntimeException(\sprintf('Directory "%s" was not created', $concurrentDirectory));
            }
        } elseif (is_file(base_path($path)) && \dirname($path) !== '.') {
            $path = \dirname((string) $path);

            if (!is_dir(storage_path(\sprintf('gitupdate/%s', $path))) && !mkdir($concurrentDirectory = storage_path(\sprintf(
                'gitupdate/%s',
                $path
            )), 0775, true) && !is_dir($concurrentDirectory)) {
                throw new RuntimeException(\sprintf('Directory "%s" was not created', $concurrentDirectory));
            }
        }
    }

    /**
     * Get the paths that need to be updated.
     *
     * @return array<string>
     */
    private function paths(): array
    {
        $p = $this->process('git diff master --name-only');
        $paths = array_filter(explode("\n", $p->getOutput()), 'strlen');

        return [...$paths, ...self::ADDITIONAL];
    }
}

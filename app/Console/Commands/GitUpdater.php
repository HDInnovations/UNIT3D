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
use FilesystemIterator;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use RuntimeException;
use Throwable;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

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
    protected $description = 'Update UNIT3D using Git';

    /**
     * Files that should be backed up and restored.
     *
     * @var array<string>
     */
    private const array ADDITIONAL_FILES = [
        '.env',
        'laravel-echo-server.json',
    ];

    /**
     * Directories that should be excluded from updates.
     *
     * @var array<string>
     */
    private const array EXCLUDED_DIRECTORIES = [
        'unit3d-announce',
        'unit3d-theme-utility',
    ];

    /**
     * List of files that were updated.
     *
     * @var array<string>
     */
    private array $updatedFiles = [];

    /**
     * Path to the log file.
     *
     * @var string
     */
    private string $logFile;

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during execution.
     */
    final public function handle(): void
    {
        $this->input = new ArgvInput();
        $this->output = new ConsoleOutput();
        $this->io = new SymfonyStyle($this->input, $this->output);

        $this->logFile = storage_path('logs/git-updater-'.now()->format('Y-m-d').'.log');
        $this->log('Starting GitUpdater');

        $this->displayBanner();

        if (!$this->confirmUpdate()) {
            return;
        }

        try {
            $this->performUpdate();
        } catch (Throwable $e) {
            $this->log('Error during update: '.$e->getMessage());
            $this->alert('error', 'Update failed');
            $this->error('Error: '.$e->getMessage());

            if ($this->io->confirm('Would you like to restore from backup?', true)) {
                $this->restoreFromBackup();
            }

            throw $e;
        }

        $this->log('Update completed successfully');
        $this->info('Please report any errors or issues.');
        $this->taskCompleted('Update process completed');
    }

    /**
     * Display the updater banner.
     */
    private function displayBanner(): void
    {
        $this->io->newLine();
        $this->io->writeln('
<fg=cyan>┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓</>
<fg=cyan>┃</><fg=green>      🚀 UNIT3D Git Updater       </><fg=cyan>┃</>
<fg=cyan>┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛</>
        ');

        $this->io->writeln('
<fg=yellow>DISCLAIMER:</> This software is provided "AS IS" without warranty of any kind.
The authors are not liable for any damages arising from the use of this software.
<fg=red>USE AT YOUR OWN RISK - MAKE SURE YOU HAVE BACKUPS!</>
        ');
        $this->io->newLine();
    }

    /**
     * Ask for confirmation to proceed with the update.
     */
    private function confirmUpdate(): bool
    {
        if (!$this->io->confirm('Would you like to proceed with the update?', true)) {
            $this->warning('Update aborted by user');
            $this->log('Update aborted by user');

            return false;
        }

        $this->io->writeln('
Press CTRL + C ANYTIME to abort!
<fg=red>Note: Aborting may leave your application in an inconsistent state.</>
        ');

        sleep(1);

        return true;
    }

    /**
     * Perform the update process.
     */
    private function performUpdate(): void
    {
        $currentVersion = $this->getCurrentVersion();

        $updatingFiles = $this->checkForUpdates();

        if (\count($updatingFiles) > 0) {
            $this->alert('info', \sprintf('Found %d Files Needing Updates', \count($updatingFiles)));

            $this->note('Files that need to be updated:');
            $this->io->listing($updatingFiles);

            if ($this->io->confirm('Start the update process?', true)) {
                $this->log('Starting update process with '.\count($updatingFiles).' files');

                $this->call('down');

                $this->execCommand('git add .');

                $pathsToBackup = $this->getPathsToBackup();

                $this->backupFiles($pathsToBackup);

                $this->header('Resetting Repository');
                $this->execCommands([
                    'git fetch origin',
                    'git reset --hard origin/master',
                ]);

                $this->restoreBackupFiles($pathsToBackup);

                $conflicts = array_intersect($updatingFiles, $pathsToBackup);

                if ($conflicts !== []) {
                    $this->warning('There are some files that were not updated because of conflicts.');
                    $this->warning('We will walk you through updating these files now.');
                    $this->manualUpdateFiles($conflicts);
                }

                $this->header('Database Migrations');

                if ($this->io->confirm('Run new database migrations?', true)) {
                    $this->runMigrations();
                }

                $this->clearApplicationCache();

                $this->header('Composer Packages');

                if ($this->io->confirm('Install/update Composer packages?', true)) {
                    $this->installComposerPackages();
                }

                $this->updateConfigurationFile();

                $this->setApplicationCache();

                $this->header('Frontend Assets');

                if ($this->io->confirm('Compile frontend assets?', true)) {
                    $this->compileAssets();
                }

                $this->setFilePermissions();

                $this->restartServices();

                $this->updatedFiles = $updatingFiles;

                $newVersion = $this->getCurrentVersion();
                $this->displayVersionInformation($currentVersion, $newVersion);
                $this->generateUpdateReport();

                $this->header('Bringing Site Back Online');
                $this->call('up');
                $this->success('Site is now online');

                if ($this->io->confirm('Remove update backups?', true)) {
                    $this->header('Cleaning Up');
                    $this->execCommand('rm -rf '.storage_path('gitupdate'));
                    $this->success('Backups deleted successfully');
                }
            } else {
                $this->alert('warning', 'Update aborted');
                $this->log('Update aborted by user after displaying files to update');
            }
        } else {
            $this->alert('success', 'No available updates found');
            $this->log('No updates available');
        }
    }

    /**
     * Check for available updates.
     *
     * @return array<string> List of files to be updated
     */
    private function checkForUpdates(): array
    {
        $this->header('Checking For Updates');
        $this->log('Checking for updates');

        $this->execCommand('git fetch origin');
        $process = $this->execCommand('git diff ..origin/master --name-only');
        $updatingFiles = array_filter(explode("\n", $process->getOutput()), 'strlen');

        $updatingFiles = array_filter($updatingFiles, fn ($file) => array_all(self::EXCLUDED_DIRECTORIES, fn ($excludedDir) => !str_starts_with($file, $excludedDir.'/')));

        $this->log('Found '.\count($updatingFiles).' files needing update');

        return $updatingFiles;
    }

    /**
     * Get the paths that need to be backed up.
     *
     * @return array<string> List of paths to backup
     */
    private function getPathsToBackup(): array
    {
        $process = $this->execCommand('git diff master --name-only');
        $paths = array_filter(explode("\n", $process->getOutput()), 'strlen');

        $paths = array_filter($paths, fn ($file) => array_all(self::EXCLUDED_DIRECTORIES, fn ($excludedDir) => !str_starts_with($file, $excludedDir.'/')));

        return [...$paths, ...self::ADDITIONAL_FILES];
    }

    /**
     * Backup files before updating.
     *
     * @param array<string> $paths Files to backup
     */
    private function backupFiles(array $paths): void
    {
        $this->header('Creating Backups');
        $this->log('Starting backup of '.\count($paths).' files/directories');

        $this->execCommands([
            'rm -rf '.storage_path('gitupdate'),
            'mkdir -p '.storage_path('gitupdate'),
        ], true);

        $this->info('Backing up '.\count($paths).' files/directories...');

        $progress = $this->io->createProgressBar(\count($paths));
        $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s% ⇒ %message%');
        $progress->setMessage('Starting backup...');
        $progress->start();

        foreach ($paths as $path) {
            $progress->setMessage($path);

            if (!file_exists(base_path($path)) && !is_dir(base_path($path))) {
                $this->log('Invalid path: '.$path);
                $progress->advance();

                continue;
            }

            $backupPath = \dirname(storage_path('gitupdate/'.$path));

            if (!is_dir($backupPath) && !mkdir($backupPath, 0775, true) && !is_dir($backupPath)) {
                $this->log('Failed to create directory: '.$backupPath);

                throw new RuntimeException(\sprintf('Directory "%s" could not be created', $backupPath));
            }

            $this->execCommand($this->copyCommand.' '.base_path($path).' '.storage_path('gitupdate/'.$path), true);

            $progress->advance();
        }

        $progress->finish();
        $this->io->newLine(2);

        $this->log('Backup completed');
        $this->taskCompleted('Backup completed');
    }

    /**
     * Restore files from backup.
     *
     * @param array<string> $paths Files to restore
     */
    private function restoreBackupFiles(array $paths): void
    {
        $this->header('Restoring Files');
        $this->log('Restoring '.\count($paths).' backup files');

        $progress = $this->io->createProgressBar(\count($paths));
        $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s% ⇒ %message%');
        $progress->setMessage('Starting restore...');
        $progress->start();

        foreach ($paths as $path) {
            $progress->setMessage($path);

            $to = Str::replaceLast('/.', '', base_path(\dirname($path)));
            $from = storage_path('gitupdate/'.$path);

            if (!file_exists($from)) {
                $progress->advance();

                continue;
            }

            if (is_dir($from)) {
                $to .= '/'.basename($from).'/';
                $from .= '/*';
            }

            $this->execCommand(\sprintf('%s %s %s', $this->copyCommand, $from, $to), true);
            $progress->advance();
        }

        $progress->finish();
        $this->io->newLine(2);

        $this->execCommands([
            'git add .',
            'git checkout origin/master -- bun.lockb',
            'git checkout origin/master -- composer.lock',
        ]);

        $this->log('Restore completed');
        $this->taskCompleted('Files restored successfully');
    }

    /**
     * Manually update files that have conflicts.
     *
     * @param array<string> $conflicts Conflicted files
     */
    private function manualUpdateFiles(array $conflicts): void
    {
        $this->header('Resolving File Conflicts');
        $this->log('Starting manual update for '.\count($conflicts).' conflicting files');

        $this->warning('Updating will cause you to LOSE any changes you might have made to these files!');

        foreach ($conflicts as $file) {
            if ($this->io->confirm(\sprintf('Update %s', $file), true)) {
                $this->execCommand(\sprintf('git checkout origin/master -- %s', $file));
                $this->success('Updated: '.$file);
                $this->log('Manually updated file: '.$file);
            } else {
                $this->note('Skipped: '.$file);
                $this->log('Skipped manual update for: '.$file);
            }
        }

        $this->taskCompleted('Conflict resolution completed');
    }

    /**
     * Run database migrations.
     */
    private function runMigrations(): void
    {
        $this->log('Running database migrations');
        $this->call('migrate');
        $this->taskCompleted('Migrations completed');
    }

    /**
     * Clear application cache.
     */
    private function clearApplicationCache(): void
    {
        $this->header('Clearing Application Cache');
        $this->log('Clearing application cache');
        $this->call('clear:all_cache');
        $this->taskCompleted('Cache cleared');
    }

    /**
     * Set application cache.
     */
    private function setApplicationCache(): void
    {
        $this->header('Setting Application Cache');
        $this->log('Setting application cache');
        $this->call('set:all_cache');
        $this->taskCompleted('Cache set');
    }

    /**
     * Install/update Composer packages.
     */
    private function installComposerPackages(): void
    {
        $this->log('Running composer commands');

        $this->execCommands([
            'composer self-update',
            'composer install --prefer-dist --no-dev -o',
        ]);

        $this->taskCompleted('Composer packages installed');
    }

    /**
     * Update the UNIT3D configuration file.
     */
    private function updateConfigurationFile(): void
    {
        $this->header('Updating Configuration');
        $this->log('Updating UNIT3D config file');
        $this->execCommand('git fetch origin && git checkout origin/master -- config/unit3d.php');
        $this->taskCompleted('Configuration updated');
    }

    /**
     * Compile frontend assets.
     */
    private function compileAssets(): void
    {
        $this->log('Running asset compilation');

        $this->execCommands([
            'bun install',
            'bun run build',
        ]);

        $this->taskCompleted('Assets compiled');
    }

    /**
     * Set proper file permissions.
     */
    private function setFilePermissions(): void
    {
        $this->header('Setting File Permissions');
        $this->log('Refreshing file permissions');
        $this->execCommand('chown -R www-data: storage bootstrap public config');
        $this->taskCompleted('Permissions set');
    }

    /**
     * Restart supervisor and PHP services.
     */
    private function restartServices(): void
    {
        $this->header('Restarting Services');
        $this->log('Restarting supervisor and PHP services');

        $this->call('queue:restart');
        $this->success('Queue workers restarted');

        $this->execCommand('supervisorctl reread && supervisorctl update && supervisorctl reload');
        $this->success('Supervisor services restarted');

        $this->execCommand('systemctl restart php8.4-fpm');
        $this->success('PHP-FPM service restarted');

        $this->taskCompleted('Services restarted');
    }

    /**
     * Get the current git version.
     */
    private function getCurrentVersion(): string
    {
        $process = $this->execCommand('git describe --tags --always');
        $version = trim($process->getOutput());

        return $version ?: 'unknown';
    }

    /**
     * Display version information before and after update.
     */
    private function displayVersionInformation(string $oldVersion, string $newVersion): void
    {
        $this->header('Version Information');

        $this->io->definitionList(
            ['Previous version' => "<fg=yellow>{$oldVersion}</>"],
            ['Current version' => "<fg=green>{$newVersion}</>"]
        );

        $this->log("Updated from version {$oldVersion} to {$newVersion}");

        if ($oldVersion === $newVersion) {
            $this->warning('No version change detected');
        } else {
            $this->success('Successfully upgraded!');
        }
    }

    /**
     * Generate an update report of what was changed.
     */
    private function generateUpdateReport(): void
    {
        $this->header('Update Report');

        $filesByType = [];

        foreach ($this->updatedFiles as $file) {
            $extension = pathinfo($file, PATHINFO_EXTENSION) ?: 'other';
            $filesByType[$extension][] = $file;
        }

        ksort($filesByType);

        $this->note('Files updated by type:');

        foreach ($filesByType as $type => $files) {
            $icon = $this->getFileTypeIcon($type);
            $this->io->section("{$icon} {$type} files (".\count($files).")");
            $this->io->listing($files);
        }

        $this->success('Update completed at: '.now()->toDateTimeString());
        $this->log('Generated update report with '.\count($this->updatedFiles).' files');
    }

    /**
     * Get an appropriate icon for file types.
     */
    private function getFileTypeIcon(string $extension): string
    {
        return match(strtolower($extension)) {
            'php' => '🐘',
            'js'  => '🟨',
            'vue' => '🟩',
            'css', 'scss', 'sass' => '🎨',
            'json' => '📝',
            'md'   => '📄',
            'jpg', 'jpeg', 'png', 'gif', 'svg' => '🖼️',
            'lock' => '🔒',
            'env', 'yml', 'yaml' => '⚙️',
            'sql'       => '🗄️',
            'gitignore' => '👁️',
            default     => '📁',
        };
    }

    /**
     * Restore from backup in case of failure.
     */
    private function restoreFromBackup(): void
    {
        $this->header('Recovery Process');
        $this->log('Attempting to restore from backup after failure');

        if (!is_dir(storage_path('gitupdate'))) {
            $this->error('No backup found to restore from!');
            $this->log('Recovery failed - no backup directory found');

            return;
        }

        $paths = [];
        $backupDir = storage_path('gitupdate');

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($backupDir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            if (!$item->isDir()) {
                $path = substr($item->getPathname(), \strlen($backupDir) + 1);
                $paths[] = $path;
            }
        }

        $this->info('Found '.\count($paths).' files to restore');
        $this->restoreBackupFiles($paths);
        $this->log('Recovery completed - restored '.\count($paths).' files from backup');

        $this->call('up');
        $this->alert('success', 'Site has been restored from backup and is back online');
    }
}

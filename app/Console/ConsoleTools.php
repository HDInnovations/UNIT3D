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

namespace App\Console;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;
use RuntimeException;

trait ConsoleTools
{
    protected SymfonyStyle $io;

    /**
     * Log a message to a file.
     */
    protected function log(string $message, ?string $logFile = null): void
    {
        $logFile ??= $this->logFile ?? storage_path('logs/console-'.now()->format('Y-m-d').'.log');
        $timestamp = now()->toDateTimeString();
        $logMessage = "[{$timestamp}] {$message}".PHP_EOL;

        if (!file_exists(\dirname($logFile)) && !mkdir(
            $concurrentDirectory = \dirname($logFile),
            0755,
            true
        ) && !is_dir($concurrentDirectory)) {
            throw new RuntimeException(\sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    /**
     * Display a section header in a quote-like format.
     */
    public function header(string $text): void
    {
        $length = mb_strlen($text) + 4; // Add padding
        $border = str_repeat('━', $length);

        $this->io->newLine();
        $this->io->writeln("<fg=blue>┏{$border}┓</>");
        $this->io->writeln("<fg=blue>┃</><fg=white>  {$text}  </><fg=blue>┃</>");
        $this->io->writeln("<fg=blue>┗{$border}┛</>");
        $this->io->newLine();

        $this->log($text);
    }

    /**
     * Display a success message.
     */
    public function success(string $message): void
    {
        $this->io->writeln("<fg=green>✓ {$message}</>");
    }

    /**
     * Display an error message.
     *
     * @param string|array<string> $string
     * @param int|string|null      $_verbosity
     */
    public function error($string, $_verbosity = null): void
    {
        if (\is_array($string)) {
            foreach ($string as $message) {
                $this->io->writeln("<fg=red>✗ {$message}</>");
            }
        } else {
            $this->io->writeln("<fg=red>✗ {$string}</>");
        }
    }

    /**
     * Display an info message.
     *
     * @param string|array<string> $string
     * @param int|string|null      $_verbosity
     */
    public function info($string, $_verbosity = null): void
    {
        if (\is_array($string)) {
            foreach ($string as $message) {
                $this->io->writeln("<fg=cyan>ℹ {$message}</>");
            }
        } else {
            $this->io->writeln("<fg=cyan>ℹ {$string}</>");
        }
    }

    /**
     * Display a warning message.
     */
    public function warning(string $message): void
    {
        $this->io->writeln("<fg=yellow>⚠ {$message}</>");
    }

    /**
     * Display a note message.
     */
    public function note(string $message): void
    {
        $this->io->writeln("<fg=magenta>• {$message}</>");
    }

    /**
     * Display a command being executed.
     */
    protected function command(string $command): void
    {
        $this->io->writeln("<fg=blue>$ <fg=yellow>{$command}</>");
    }

    /**
     * Execute a shell command with a progress bar.
     */
    protected function execCommand(string $command, bool $silent = false): Process
    {
        if (!$silent) {
            $this->io->newLine();
            $this->command($command);
            $progressBar = $this->createProgressBar();
        }

        $process = Process::fromShellCommandline($command);
        $process->setTimeout(3600);
        $process->start();

        while ($process->isRunning()) {
            try {
                $process->checkTimeout();
            } catch (ProcessTimedOutException) {
                $this->error("Command timed out after 1 hour: '{$command}'");
            }

            if (!$silent) {
                $progressBar->advance();
            }

            usleep(200000);
        }

        if (!$silent) {
            $progressBar->finish();
            $this->io->newLine();

            if ($process->isSuccessful()) {
                $this->success("Command completed successfully!");
            } else {
                $this->error("Command failed: ".$process->getErrorOutput());
            }
        }

        $process->stop();

        return $process;
    }

    /**
     * Execute multiple shell commands.
     *
     * @param array<string> $commands
     */
    protected function execCommands(array $commands, bool $silent = false): void
    {
        foreach ($commands as $command) {
            $process = $this->execCommand($command, $silent);

            if (!$silent && $process->getOutput() && trim($process->getOutput()) !== '') {
                $this->io->writeln("<fg=gray>".trim($process->getOutput())."</>");
            }
        }
    }

    /**
     * Create a stylized progress bar.
     */
    protected function createProgressBar(): ProgressBar
    {
        $progressBar = $this->io->createProgressBar();
        $progressBar->setBarCharacter('<fg=cyan>▶</>');
        $progressBar->setEmptyBarCharacter('<fg=gray>▷</>');
        $progressBar->setProgressCharacter('<fg=green>▶</>');
        $progressBar->setFormat(' %bar% <fg=cyan>%percent:3s%%</> %elapsed:6s%/%estimated:-6s% ');
        $progressBar->start();

        return $progressBar;
    }

    /**
     * Display a stylized alert box.
     *
     * @param string|array<string> $string
     * @param int|string|null      $verbosity
     */
    public function alert($string, $verbosity = null): void
    {
        if (\is_string($string) && \in_array($string, ['success', 'error', 'warning', 'info']) && $verbosity !== null && \is_string($verbosity)) {
            $this->renderAlert($verbosity, $string);

            return;
        }

        if (\is_array($string)) {
            foreach ($string as $message) {
                $this->renderAlert($message);
            }
        } else {
            $this->renderAlert($string);
        }
    }

    /**
     * Render a stylized alert box with type detection.
     *
     * @param string      $message The message to display
     * @param string|null $type    Optional alert type (success, error, warning, info)
     */
    protected function renderAlert(string $message, ?string $type = null): void
    {
        if ($type === null) {
            $detectedType = 'info';

            if (preg_match('/^(success|error|warning|info):/i', $message, $matches)) {
                $detectedType = strtolower($matches[1]);
                $message = trim(substr($message, \strlen($matches[1]) + 1));
            }

            $type = $detectedType;
        }

        $style = match ($type) {
            'success' => 'green',
            'error'   => 'red',
            'warning' => 'yellow',
            'info'    => 'cyan',
            default   => 'white'
        };

        $icon = match ($type) {
            'success' => '✓',
            'error'   => '✗',
            'warning' => '⚠',
            'info'    => 'ℹ',
            default   => '•'
        };

        $this->io->newLine();
        $this->io->writeln("<fg={$style}>{$icon} {$message}  </>");
        $this->io->newLine();
    }

    /**
     * Display a task completion message.
     */
    protected function taskCompleted(string $message = 'Done'): void
    {
        $this->success($message);
        $this->io->newLine();
    }
}

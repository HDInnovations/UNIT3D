<?php
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

trait ConsoleTools
{
    /**
     * @var SymfonyStyle
     */
    protected $io;

    private function cyan($line)
    {
        $this->io->writeln(\sprintf('<fg=cyan>%s</>', $line));
    }

    private function white($line)
    {
        $this->io->writeln(PHP_EOL.$line);
    }

    private function magenta($line)
    {
        $this->io->writeln(\sprintf('<fg=magenta>%s</>', $line));
    }

    private function green($line)
    {
        $this->io->writeln(\sprintf('<fg=green>%s</>', $line));
    }

    private function red($line)
    {
        $this->io->writeln(\sprintf('<fg=red>%s</>', $line));
    }

    private function blue($line)
    {
        $this->io->writeln(\sprintf('<fg=blue>%s</>', $line));
    }

    private function done()
    {
        $this->green('<fg=white>[</>Done<fg=white>]</>');
    }

    private function header($line)
    {
        $this->blue(\str_repeat('=', 50));
        $this->io->write($line);
        $this->blue(\str_repeat('=', 50));
    }

    private function alertSuccess($line)
    {
        $this->io->writeln(\sprintf('<fg=white>[</><fg=green> !! %s !! </><fg=white>]</>', $line));
    }

    private function alertDanger($line)
    {
        $this->io->writeln(\sprintf('<fg=white>[</><fg=red> !! %s !! </><fg=white>]</>', $line));
    }

    private function alertInfo($line)
    {
        $this->io->writeln(\sprintf('<fg=white>[</><fg=cyan> !! %s !! </><fg=white>]</>', $line));
    }

    private function alertWarning($line)
    {
        $this->io->writeln(\sprintf('<fg=white>[</><fg=yellow> !! %s !! </><fg=white>]</>', $line));
    }

    private function commands(array $commands, $silent = false)
    {
        foreach ($commands as $command) {
            $process = $this->process($command, $silent);

            if (! $silent) {
                echo "\n\n";
                $this->warn($process->getOutput());
            }
        }
    }

    private function process($command, $silent = false)
    {
        if (! $silent) {
            $this->cyan($command);
            $bar = $this->progressStart();
        }

        $process = Process::fromShellCommandline($command);
        $process->setTimeout(3_600);
        $process->start();

        while ($process->isRunning()) {
            try {
                $process->checkTimeout();
            } catch (ProcessTimedOutException) {
                $this->red(\sprintf("'%s' timed out.!", $command));
            }

            if (! $silent) {
                $bar->advance();
            }

            \usleep(200_000);
        }

        if (! $silent) {
            $this->progressStop($bar);
        }

        $process->stop();

        if (! $process->isSuccessful()) {
            $this->red($process->getErrorOutput());
            //die();
        }

        return $process;
    }

    /**
     * @return ProgressBar
     */
    protected function progressStart()
    {
        $bar = $this->io->createProgressBar();
        $bar->setBarCharacter('<fg=magenta>=</>');
        $bar->setFormat('[%bar%] (<fg=cyan>%message%</>)');
        $bar->setMessage('Please Wait ...');
        //$bar->setRedrawFrequency(20); todo: may be useful for platforms like CentOS
        $bar->start();

        return $bar;
    }

    protected function progressStop(ProgressBar $progressBar)
    {
        $progressBar->setMessage('<fg=green>Done!</>');
        $progressBar->finish();
    }
}

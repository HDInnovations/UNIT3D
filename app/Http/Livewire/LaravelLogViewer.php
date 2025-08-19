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

namespace App\Http\Livewire;

use App\Traits\CastLivewireProperties;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\File;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use SplFileInfo;

class LaravelLogViewer extends Component
{
    use CastLivewireProperties;
    use WithPagination;

    /**
     * @var int[]|string[]
     */
    public array $logs = [0];

    public int $page = 1;

    #[Url(history: true)]
    public int $perPage = 5;

    final public function updating(string $field, mixed &$value): void
    {
        $this->castLivewireProperties($field, $value);
    }

    final public function loadMore(): void
    {
        $this->perPage += 5;
    }

    /**
     * @var \Illuminate\Support\Collection<int, \Symfony\Component\Finder\SplFileInfo>
     */
    final protected \Illuminate\Support\Collection $logFiles {
        get => collect(File::allFiles(storage_path('logs')))
            ->sortByDesc(fn (SplFileInfo $file) => $file->getMTime())
            ->values();
    }

    /**
     * @var LengthAwarePaginator<string, \Illuminate\Support\Collection<int, mixed>>
     */
    final protected LengthAwarePaginator $entries {
        get {
            $files = $this->logFiles;
            $logString = '';

            foreach ($this->logs as $log) {
                if ($files[$log] ?? []) {
                    $logString .= file_get_contents($files[$log]->getPathname());
                }
            }

            $entryPattern = '/^\[(?<date>.*)\]\s(?<env>\w+)\.(?<level>\w+)\:\s/m';
            $contextPattern = '/^(?<message>[^\{]*)?(?:\{"exception"\:"\[object\]\s\((?<exception>[^\s\(]+))?.*\s(?:in|at)\s(?<in>.*)\:(?<line>\d+)\)?/ms';

            $entries = collect();

            if (preg_match_all($entryPattern, $logString, $entryMatches, PREG_SET_ORDER) !== false) {
                $stacktraces = preg_split($entryPattern, $logString);
                // Delete the empty first entry
                array_shift($stacktraces);
                $numEntries = \count($entryMatches);

                for ($i = 0; $i < $numEntries; $i++) {
                    // The context is the portion before the first stack trace
                    $context = preg_split('/^\[stacktrace\]|Stack trace\:/ms', (string) $stacktraces[$i])[0];
                    // The `context` consists of a message, an exception, a filename, and a line count
                    preg_match($contextPattern, $context, $contextMatches);

                    $entries->push([
                        'date'       => $entryMatches[$i]['date'],
                        'env'        => $entryMatches[$i]['env'],
                        'level'      => $entryMatches[$i]['level'],
                        'message'    => $contextMatches['message'] ?? '',
                        'exception'  => $contextMatches['exception'] ?? '',
                        'in'         => $contextMatches['in'] ?? '',
                        'line'       => $contextMatches['line'] ?? '',
                        'stacktrace' => $stacktraces[$i],
                    ]);
                }
            }

            $groupedEntries = $entries->groupBy(fn ($entry) => $entry['message'].'-'.$entry['exception'].'-'.$entry['in'].'-'.$entry['line']);
            $currentEntries = $groupedEntries->forPage($this->page, $this->perPage);

            return new LengthAwarePaginator($currentEntries, $groupedEntries->count(), $this->perPage, $this->page);
        }
    }

    final public function clearLatestLog(): void
    {
        $latestLogFile = $this->logFiles->first();

        if ($latestLogFile) {
            File::put($latestLogFile->getPathname(), '');
        }
    }

    final public function deleteAllLogs(): void
    {
        $directory = storage_path('logs');
        $files = File::allFiles($directory);

        foreach ($files as $file) {
            File::delete($file->getPathname());
        }
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.laravel-log-viewer', [
            'files'   => $this->logFiles,
            'entries' => $this->entries,
        ])
            ->extends('layout.default')
            ->section('content');
    }
}

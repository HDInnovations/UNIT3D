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

use App\Jobs\ProcessBackup;
use App\Rules\BackupDisk;
use App\Rules\PathToZip;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Spatie\Backup\BackupDestination\Backup;
use Spatie\Backup\BackupDestination\BackupDestination;
use Spatie\Backup\Config\MonitoredBackupsConfig;
use Spatie\Backup\Helpers\Format;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatus;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatusFactory;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupPanel extends Component
{
    /**
     * @var array<string, string>
     */
    protected $listeners = ['refreshBackups' => '$refresh'];

    /**
     * @var array<mixed>
     */
    final protected array $backupStatuses {
        get => BackupDestinationStatusFactory::createForMonitorConfig(MonitoredBackupsConfig::fromArray(config('backup.monitor_backups')))
            ->map(fn (BackupDestinationStatus $backupDestinationStatus) => [
                'name'      => $backupDestinationStatus->backupDestination()->backupName(),
                'disk'      => $backupDestinationStatus->backupDestination()->diskName(),
                'reachable' => $backupDestinationStatus->backupDestination()->isReachable(),
                'healthy'   => $backupDestinationStatus->isHealthy(),
                'amount'    => $backupDestinationStatus->backupDestination()->backups()->count(),
                'newest'    => $backupDestinationStatus->backupDestination()->newestBackup() !== null
                    ? $backupDestinationStatus->backupDestination()->newestBackup()->date()->diffForHumans()
                    : 'No backups present',
                'usedStorage' => Format::humanReadableSize($backupDestinationStatus->backupDestination()->usedStorage()),
            ])
            ->values()
            ->toArray();
    }

    final protected ?string $activeDisk {
        get => $this->backupStatuses[0]['disk'] ?? null;
    }

    /**
     * @var array<mixed>
     */
    final protected array $disks {
        get => collect($this->backupStatuses)
            ->map(fn ($backupStatus): mixed => $backupStatus['disk'])
            ->values()
            ->all();
    }

    /**
     * @var    array<mixed>
     * @throws ValidationException
     */
    final protected array $backups {
        get {
            $this->validateActiveDisk();

            return BackupDestination::create($this->activeDisk, config('backup.backup.name'))
                ->backups()
                ->map(fn (Backup $backup) => [
                    'path' => $backup->path(),
                    'date' => $backup->date()->format('Y-m-d H:i:s'),
                    'size' => Format::humanReadableSize($backup->sizeInBytes()),
                ])
                ->toArray();
        }
    }

    final public function deleteBackup(int $fileIndex): void
    {
        $deletingFile = $this->backups[$fileIndex];

        $this->validateActiveDisk();
        $this->validateFilePath($deletingFile ? $deletingFile['path'] : '');

        $backupDestination = BackupDestination::create($this->activeDisk, config('backup.backup.name'));

        $backupDestination
            ->backups()
            ->first(fn (Backup $backup) => $backup->path() === $deletingFile['path'])
            ->delete();

        $this->dispatch('refreshBackups');
    }

    /**
     * @throws ValidationException
     */
    final public function downloadBackup(string $filePath): Response|StreamedResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $this->validateActiveDisk();
        $this->validateFilePath($filePath);

        $backupDestination = BackupDestination::create($this->activeDisk, config('backup.backup.name'));

        $backup = $backupDestination->backups()->first(fn (Backup $backup) => $backup->path() === $filePath);

        if (!$backup) {
            return response('Backup not found', ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        $fileName = pathinfo((string) $backup->path(), PATHINFO_BASENAME);
        $size = $backup->sizeInBytes();

        $downloadHeaders = [
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Type'        => 'application/zip',
            'Content-Length'      => $size,
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
            'Pragma'              => 'public',
        ];

        return response()->stream(function () use ($backup): void {
            $stream = $backup->stream();

            fpassthru($stream);

            if (\is_resource($stream)) {
                fclose($stream);
            }
        }, 200, $downloadHeaders);
    }

    final public function createBackup(string $option = ''): void
    {
        dispatch(new ProcessBackup($option));
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.backup-panel');
    }

    /**
     * @throws ValidationException
     */
    private function validateActiveDisk(): void
    {
        try {
            Validator::make(
                ['activeDisk' => $this->activeDisk],
                [
                    'activeDisk' => ['required', new BackupDisk()],
                ],
                [
                    'activeDisk.required' => 'Select a disk',
                ]
            )->validate();
        } catch (ValidationException $e) {
            $message = $e->validator->errors()->get('activeDisk')[0];
            $this->dispatch('showErrorToast', message: $message)->self();

            throw $e;
        }
    }

    /**
     * @throws ValidationException
     */
    private function validateFilePath(string $filePath): void
    {
        try {
            Validator::make(
                ['file' => $filePath],
                [
                    'file' => ['required', new PathToZip()],
                ],
                [
                    'file.required' => 'Select a file',
                ]
            )->validate();
        } catch (ValidationException $e) {
            $message = $e->validator->errors()->get('file')[0];
            $this->dispatch('showErrorToast', message: $message)->self();

            throw $e;
        }
    }
}

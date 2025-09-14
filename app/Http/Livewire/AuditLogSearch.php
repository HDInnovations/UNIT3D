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

use App\Models\Audit;
use App\Traits\Auditable;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use JsonException;
use ReflectionClass;

class AuditLogSearch extends Component
{
    use WithPagination;

    #TODO: Update URL attributes once Livewire 3 fixes upstream bug. See: https://github.com/livewire/livewire/discussions/7746

    #[Url(history: true)]
    public string $username = '';

    #[Url(history: true)]
    public string $modelName = '';

    #[Url(history: true)]
    public string $modelId = '';

    #[Url(history: true)]
    public string $action = '';

    #[Url(history: true)]
    public string $record = '';

    #[Url(history: true)]
    public int $perPage = 25;

    #[Url(history: true)]
    public string $sortField = 'created_at';

    #[Url(history: true)]
    public string $sortDirection = 'desc';

    /**
     * @var string[]
     */
    final protected array $modelNames {
        get => collect(scandir(app_path('Models')) ?: [])
            ->filter(fn ($path) => str_ends_with($path, '.php'))
            ->map(fn ($path) => 'App\\Models\\'.basename($path, '.php'))
            ->filter(fn ($class) => class_exists($class))
            ->map(fn ($class) => new ReflectionClass($class))
            ->filter(fn ($class) => \in_array(Auditable::class, array_keys($class->getTraits())))
            ->map(fn ($class) => $class->getShortName())
            ->toArray();
    }

    /**
     * @throws JsonException
     * @var    \Illuminate\Pagination\LengthAwarePaginator<int, Audit>
     */
    final protected \Illuminate\Pagination\LengthAwarePaginator $audits {
        get => Audit::query()
            ->with('user')
            ->when($this->username, fn ($query) => $query->whereRelation('user', 'username', '=', $this->username))
            ->when($this->modelName, fn ($query) => $query->where('auditable_type', '=', 'App\\Models\\'.$this->modelName))
            ->when($this->modelId, fn ($query) => $query->where('auditable_id', '=', $this->modelId))
            ->when($this->action, fn ($query) => $query->where('action', '=', $this->action))
            ->when($this->record, fn ($query) => $query->where('record', 'LIKE', '%'.$this->record.'%'))
            ->latest()
            ->paginate($this->perPage)
            ->through(function ($audit) {
                $audit->values = json_decode((string) $audit->record, true, 512, JSON_THROW_ON_ERROR);

                return $audit;
            });
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.audit-log-search', [
            'audits'     => $this->audits,
            'modelNames' => $this->modelNames,
        ]);
    }
}

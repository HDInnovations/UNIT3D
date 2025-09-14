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

namespace App\Traits;

use App\Models\Audit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use ArgumentCountError;
use InvalidArgumentException;
use JsonException;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function (Model $model): void {
            self::registerCreate($model);
        });

        static::updated(function (Model $model): void {
            self::registerUpdate($model);
        });

        static::deleted(function (Model $model): void {
            self::registerDelete($model);
        });
    }

    /**
     * Generates the data to store.
     *
     * @param mixed[] $old
     * @param mixed[] $new
     *
     * @throws JsonException
     */
    protected static function generate(string $action, array $old = [], array $new = []): false|string
    {
        $data = [];

        switch ($action) {
            case 'create':
                // Expect new data to be filled
                throw_if(empty($new), new ArgumentCountError('Action `create` expects new data.'));

                // Process
                foreach ($new as $key => $value) {
                    $data[$key] = [
                        'old' => null,
                        'new' => $value,
                    ];
                }

                break;
            case 'update':
                // Expect old and new data to be filled
                /*if (empty($old) || empty($new)) {
                    throw new \ArgumentCountError('Action `update` expects both old and new data.');
                }*/
                // Process only what changed
                foreach ($new as $key => $value) {
                    $data[$key] = [
                        'old' => $old[$key],
                        'new' => $value,
                    ];
                }

                break;
            case 'delete':
                // Expect new data to be filled
                throw_if(empty($old), new ArgumentCountError('Action `delete` expects new data.'));

                // Process
                foreach ($old as $key => $value) {
                    $data[$key] = [
                        'old' => $value,
                        'new' => null,
                    ];
                }

                break;
            default:
                throw new InvalidArgumentException(\sprintf('Unknown action `%s`.', $action));
        }

        return json_encode($data, JSON_THROW_ON_ERROR);
    }

    /**
     * Strips specified data keys from the audit.
     *
     * @param  mixed[] $data
     * @return mixed[]
     */
    protected static function strip(Model $model, array $data): array
    {
        // Initialize an instance of $model
        $instance = new $model();
        // Start stripping
        $globalDiscards = (empty(config('audit.global_discards'))) ? [] : config('audit.global_discards');
        $modelDiscards = (empty($instance->discarded)) ? [] : $instance->discarded;

        foreach (array_keys($data) as $key) {
            // Check the model-specific discards
            if (\in_array($key, $modelDiscards, true)) {
                unset($data[$key]);
            }

            // Check global discards
            if (!empty($globalDiscards) && \in_array($key, $globalDiscards, true)) {
                unset($data[$key]);
            }
        }

        // Return
        return $data;
    }

    /**
     * Gets the current user ID, or null if guest.
     */
    public static function getUserId(): ?int
    {
        if (auth()->guest()) {
            return null;
        }

        return auth()->user()->id;
    }

    /**
     * Logs a record creation.
     *
     * @throws JsonException
     */
    protected static function registerCreate(Model $model): void
    {
        // Get auth (if any)
        $userId = self::getUserId();

        // Generate the JSON to store
        $data = self::generate('create', [], self::strip($model, $model->getAttributes()));

        if (null !== $userId && !empty($data)) {
            // Store record
            $now = Carbon::now()->format('Y-m-d H:i:s');
            DB::table('audits')->insert([
                'user_id'        => $userId,
                'auditable_type' => $model::class,
                'auditable_id'   => $model->{$model->getKeyName()},
                'action'         => 'create',
                'record'         => $data,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }
    }

    /**
     * Logs a record update.
     *
     * @throws JsonException
     */
    protected static function registerUpdate(Model $model): void
    {
        // Get auth (if any)
        $userId = self::getUserId();

        // Generate the JSON to store
        $data = self::generate('update', self::strip($model, $model->getOriginal()), self::strip($model, $model->getChanges()));

        if (null !== $userId && false !== $data && !empty(json_decode($data, true, 512, JSON_THROW_ON_ERROR))) {
            // Store record
            $now = Carbon::now()->format('Y-m-d H:i:s');
            DB::table('audits')->insert([
                'user_id'        => $userId,
                'auditable_type' => $model::class,
                'auditable_id'   => $model->{$model->getKeyName()},
                'action'         => 'update',
                'record'         => $data,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }
    }

    /**
     * Logs a record deletion.
     *
     * @throws JsonException
     */
    protected static function registerDelete(Model $model): void
    {
        // Get auth (if any)
        $userId = self::getUserId();

        // Generate the JSON to store
        $data = self::generate('delete', self::strip($model, $model->getAttributes()));

        if (null !== $userId && !empty($data)) {
            // Store record
            $now = Carbon::now()->format('Y-m-d H:i:s');
            DB::table('audits')->insert([
                'user_id'        => $userId,
                'auditable_type' => $model::class,
                'auditable_id'   => $model->{$model->getKeyName()},
                'action'         => 'delete',
                'record'         => $data,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Audit, $this>
     */
    public function audits(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Audit::class, 'auditable');
    }
}

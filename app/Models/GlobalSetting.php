<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Database-backed global application setting.
 */
final class GlobalSetting extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'key',
        'value',
    ];

    public static function value(string $key, mixed $default = null): mixed
    {
        return cache()->rememberForever(
            'global-setting:'.$key,
            fn () => self::query()->where('key', '=', $key)->value('value') ?? $default,
        );
    }

    public static function put(string $key, mixed $value): void
    {
        self::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        cache()->forget('global-setting:'.$key);
    }
}

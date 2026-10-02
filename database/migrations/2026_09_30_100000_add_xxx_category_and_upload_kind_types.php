<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * Categories seeded here, keyed by name. Purely additive: existing rows
     * (by name) are never touched, renamed, or removed.
     *
     * @var array<string, array{icon: string, meta: string}>
     */
    private const array NEW_CATEGORIES = [
        'XXX' => ['icon' => 'fa-triangle-exclamation', 'meta' => 'no_meta'],
    ];

    /**
     * Types seeded here, keyed by name. Purely additive.
     *
     * @var list<string>
     */
    private const array NEW_TYPES = ['PC', 'Console', 'Lossless', 'Lossy', 'Other'];

    public function up(): void
    {
        $faIcon = config('other.font-awesome');

        $nextCategoryPosition = (int) (DB::table('categories')->max('position') ?? -1) + 1;

        foreach (self::NEW_CATEGORIES as $name => $definition) {
            if (DB::table('categories')->where('name', '=', $name)->exists()) {
                continue;
            }

            DB::table('categories')->insert([
                'name'       => $name,
                'position'   => $nextCategoryPosition,
                'icon'       => $faIcon.' '.$definition['icon'],
                'image'      => null,
                'movie_meta' => false,
                'tv_meta'    => false,
                'game_meta'  => false,
                'music_meta' => false,
                'book_meta'  => false,
                'no_meta'    => $definition['meta'] === 'no_meta',
            ]);

            $nextCategoryPosition++;
        }

        $nextTypePosition = (int) (DB::table('types')->max('position') ?? -1) + 1;

        foreach (self::NEW_TYPES as $name) {
            if (DB::table('types')->where('name', '=', $name)->exists()) {
                continue;
            }

            DB::table('types')->insert([
                'name'     => $name,
                'position' => $nextTypePosition,
            ]);

            $nextTypePosition++;
        }
    }

    public function down(): void
    {
        // These rows may predate the migration or already belong to uploads.
        // Keep reference data when rolling back application code.
        throw new LogicException('Upload category/type reference data cannot be rolled back safely.');
    }
};

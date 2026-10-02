<?php

declare(strict_types=1);

namespace App\Services\Metadata;

use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/** @phpstan-import-type LookupResult from TorrentMetadataLookup */
final class MetadataSelectionStore
{
    /** @param LookupResult $lookup */
    public function remember(User $user, Category $category, array $lookup): string
    {
        $token = (string) Str::uuid();
        Cache::put('metadata-selection:'.$token, [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'lookup' => $lookup,
        ], now()->addHour());

        return $token;
    }

    /** @return LookupResult */
    public function retrieve(User $user, Category $category, string $token): array
    {
        $selection = Cache::get('metadata-selection:'.$token);

        if (!\is_array($selection)
            || ($selection['user_id'] ?? null) !== $user->id
            || ($selection['category_id'] ?? null) !== $category->id
            || !\is_array($selection['lookup'] ?? null)) {
            throw ValidationException::withMessages([
                'metadata_selection_token' => __('metadata.errors.selection-expired'),
            ]);
        }

        return $selection['lookup'];
    }
}

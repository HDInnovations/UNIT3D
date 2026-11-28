<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Helpers\Language;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Url;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationRule;

#[MergeValidationRules]
class UpdateGeneralSettingRequest extends Data
{
    public function __construct(
        public bool $newsBlockVisible,
        public int $newsBlockPosition,
        public bool $chatBlockVisible,
        public int $chatBlockPosition,
        public bool $featuredBlockVisible,
        public int $featuredBlockPosition,
        public bool $randomMediaBlockVisible,
        public int $randomMediaBlockPosition,
        public bool $pollBlockVisible,
        public int $pollBlockPosition,
        public bool $topTorrentsBlockVisible,
        public int $topTorrentsBlockPosition,
        public bool $topUsersBlockVisible,
        public int $topUsersBlockPosition,
        public bool $latestTopicsBlockVisible,
        public int $latestTopicsBlockPosition,
        public bool $latestPostsBlockVisible,
        public int $latestPostsBlockPosition,
        public bool $latestCommentsBlockVisible,
        public int $latestCommentsBlockPosition,
        public bool $onlineBlockVisible,
        public int $onlineBlockPosition,
        public string $locale,
        public int $style,
        #[Url('https')]
        public ?string $customCss,
        #[Url('https')]
        public ?string $standaloneCss,
        #[In([0, 1, 2, 3])]
        public int $torrentLayout,
        #[In(['created_at', 'bumped_at'])]
        public string $torrentSortField,
        public bool $torrentSearchAutofocus,
        public bool $showPoster,
        public bool $unbookmarkTorrentsOnCompletion,
        public bool $autoFreeleechApply,
        #[Min(0)]
        public int $autoFreeleechMinTokens,
        public bool $showAdultContent,
    ) {
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, list<ValidationRule>>
     */
    public static function rules(): array
    {
        return [
            'locale' => [
                new In(array_keys(Language::allowed())),
            ],
        ];
    }
}

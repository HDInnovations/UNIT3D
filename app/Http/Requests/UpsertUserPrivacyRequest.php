<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\UserGroup;
use App\Models\Group;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;

#[MergeValidationRules]
class UpsertUserPrivacyRequest extends Data
{
    public function __construct(
        public bool $showAchievement,
        public bool $showDownload,
        public bool $showFollower,
        public bool $showOnline,
        public bool $showPeer,
        public bool $showPost,
        public bool $showProfileAbout,
        public bool $showProfileAchievement,
        public bool $showProfileBadge,
        public bool $showProfileFollower,
        public bool $showProfileTitle,
        public bool $showProfileBonExtra,
        public bool $showProfileCommentExtra,
        public bool $showProfileForumExtra,
        public bool $showProfileRequestExtra,
        public bool $showProfileTorrentCount,
        public bool $showProfileTorrentExtra,
        public bool $showProfileTorrentRatio,
        public bool $showProfileTorrentSeed,
        public bool $showProfileWarning,
        public bool $showRequested,
        public bool $showTopic,
        public bool $showUpload,
        public bool $privateProfile,
        public bool $hidden,
        /** @var list<int> */
        public array $jsonProfileGroups = [],
        /** @var list<int> */
        public array $jsonTorrentGroups = [],
        /** @var list<int> */
        public array $jsonForumGroups = [],
        /** @var list<int> */
        public array $jsonBonGroups = [],
        /** @var list<int> */
        public array $jsonCommentGroups = [],
        /** @var list<int> */
        public array $jsonWishlistGroups = [],
        /** @var list<int> */
        public array $jsonFollowerGroups = [],
        /** @var list<int> */
        public array $jsonAchievementGroups = [],
        /** @var list<int> */
        public array $jsonRankGroups = [],
        /** @var list<int> */
        public array $jsonRequestGroups = [],
        /** @var list<int> */
        public array $jsonOtherGroups = [],
    ) {
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|\Illuminate\Validation\Rules\In|string>
     */
    public static function rules(): array
    {
        $validGroups = Group::query()
            ->where('is_modo', '=', false)
            ->where('is_admin', '=', false)
            ->where('id', '!=', UserGroup::VALIDATING->value)
            ->where('id', '!=', UserGroup::PRUNED->value)
            ->where('id', '!=', UserGroup::BANNED->value)
            ->where('id', '!=', UserGroup::DISABLED->value)
            ->pluck('id');

        return [
            'json_profile_groups.*'     => Rule::in($validGroups),
            'json_torrent_groups.*'     => Rule::in($validGroups),
            'json_forum_groups.*'       => Rule::in($validGroups),
            'json_bon_groups.*'         => Rule::in($validGroups),
            'json_comment_groups.*'     => Rule::in($validGroups),
            'json_wishlist_groups.*'    => Rule::in($validGroups),
            'json_follower_groups.*'    => Rule::in($validGroups),
            'json_achievement_groups.*' => Rule::in($validGroups),
            'json_rank_groups.*'        => Rule::in($validGroups),
            'json_request_groups.*'     => Rule::in($validGroups),
            'json_other_groups.*'       => Rule::in($validGroups),
        ];
    }
}

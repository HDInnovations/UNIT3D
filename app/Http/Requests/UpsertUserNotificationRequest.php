<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\UserGroup;
use App\Models\Group;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;

#[MergeValidationRules]
class UpsertUserNotificationRequest extends Data
{
    public function __construct(
        public bool $showAccountFollow,
        public bool $showAccountUnfollow,
        public bool $showFollowingUpload,
        public bool $showBonGift,
        public bool $showSubscriptionForum,
        public bool $showSubscriptionTopic,
        public bool $showRequestComment,
        public bool $showRequestBounty,
        public bool $showRequestFill,
        public bool $showRequestFillApprove,
        public bool $showRequestFillReject,
        public bool $showRequestClaim,
        public bool $showRequestUnclaim,
        public bool $showTorrentComment,
        public bool $showTorrentThank,
        public bool $showTorrentTip,
        public bool $showMentionTorrent_comment,
        public bool $showMentionRequest_comment,
        public bool $showMentionArticle_comment,
        public bool $showMentionForum_post,
        public bool $showForumTopic,
        /** @var list<int> */
        public array $jsonAccountGroups,
        /** @var list<int> */
        public array $jsonBonGroups,
        /** @var list<int> */
        public array $jsonFollowingGroups,
        /** @var list<int> */
        public array $jsonForumGroups,
        /** @var list<int> */
        public array $jsonRequestGroups,
        /** @var list<int> */
        public array $jsonSubscriptionGroups,
        /** @var list<int> */
        public array $jsonTorrentGroups,
        /** @var list<int> */
        public array $jsonMentionGroups,
        public bool $blockNotifications,
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
            'json_account_groups.*'      => Rule::in($validGroups),
            'json_bon_groups.*'          => Rule::in($validGroups),
            'json_following_groups.*'    => Rule::in($validGroups),
            'json_forum_groups.*'        => Rule::in($validGroups),
            'json_request_groups.*'      => Rule::in($validGroups),
            'json_subscription_groups.*' => Rule::in($validGroups),
            'json_torrent_groups.*'      => Rule::in($validGroups),
            'json_mention_groups.*'      => Rule::in($validGroups),
        ];
    }
}

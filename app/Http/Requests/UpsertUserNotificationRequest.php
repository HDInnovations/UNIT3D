<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\UserGroup;
use App\Models\Group;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class UpsertUserNotificationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|\Illuminate\Validation\Rules\In|string>
     */
    public function rules(): array
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
            'show_account_follow'          => 'required|boolean',
            'show_account_unfollow'        => 'required|boolean',
            'show_following_upload'        => 'required|boolean',
            'show_bon_gift'                => 'required|boolean',
            'show_subscription_forum'      => 'required|boolean',
            'show_subscription_topic'      => 'required|boolean',
            'show_request_comment'         => 'required|boolean',
            'show_request_bounty'          => 'required|boolean',
            'show_request_fill'            => 'required|boolean',
            'show_request_fill_approve'    => 'required|boolean',
            'show_request_fill_reject'     => 'required|boolean',
            'show_request_claim'           => 'required|boolean',
            'show_request_unclaim'         => 'required|boolean',
            'show_torrent_comment'         => 'required|boolean',
            'show_torrent_thank'           => 'required|boolean',
            'show_torrent_tip'             => 'required|boolean',
            'show_mention_torrent_comment' => 'required|boolean',
            'show_mention_request_comment' => 'required|boolean',
            'show_mention_article_comment' => 'required|boolean',
            'show_mention_forum_post'      => 'required|boolean',
            'show_forum_topic'             => 'required|boolean',
            'json_account_groups'          => 'array',
            'json_account_groups.*'        => Rule::in($validGroups),
            'json_bon_groups'              => 'array',
            'json_bon_groups.*'            => Rule::in($validGroups),
            'json_following_groups'        => 'array',
            'json_following_groups.*'      => Rule::in($validGroups),
            'json_forum_groups'            => 'array',
            'json_forum_groups.*'          => Rule::in($validGroups),
            'json_request_groups'          => 'array',
            'json_request_groups.*'        => Rule::in($validGroups),
            'json_subscription_groups'     => 'array',
            'json_subscription_groups.*'   => Rule::in($validGroups),
            'json_torrent_groups'          => 'array',
            'json_torrent_groups.*'        => Rule::in($validGroups),
            'json_mention_groups'          => 'array',
            'json_mention_groups.*'        => Rule::in($validGroups),
            'block_notifications'          => 'required|boolean',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    #[Override]
    protected function prepareForValidation(): void
    {
        $this->merge([
            'json_account_groups'      => array_map(intval(...), $this->json_account_groups ?? []),
            'json_bon_groups'          => array_map(intval(...), $this->json_bon_groups ?? []),
            'json_following_groups'    => array_map(intval(...), $this->json_following_groups ?? []),
            'json_forum_groups'        => array_map(intval(...), $this->json_forum_groups ?? []),
            'json_request_groups'      => array_map(intval(...), $this->json_request_groups ?? []),
            'json_subscription_groups' => array_map(intval(...), $this->json_subscription_groups ?? []),
            'json_torrent_groups'      => array_map(intval(...), $this->json_torrent_groups ?? []),
            'json_mention_groups'      => array_map(intval(...), $this->json_mention_groups ?? []),
        ]);
    }
}

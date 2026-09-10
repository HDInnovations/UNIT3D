<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\UserGroup;
use App\Models\Group;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class UpsertUserPrivacyRequest extends FormRequest
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
            'show_achievement'           => 'required|boolean',
            'show_download'              => 'required|boolean',
            'show_follower'              => 'required|boolean',
            'show_online'                => 'required|boolean',
            'show_peer'                  => 'required|boolean',
            'show_post'                  => 'required|boolean',
            'show_profile_about'         => 'required|boolean',
            'show_profile_achievement'   => 'required|boolean',
            'show_profile_badge'         => 'required|boolean',
            'show_profile_follower'      => 'required|boolean',
            'show_profile_title'         => 'required|boolean',
            'show_profile_bon_extra'     => 'required|boolean',
            'show_profile_comment_extra' => 'required|boolean',
            'show_profile_forum_extra'   => 'required|boolean',
            'show_profile_request_extra' => 'required|boolean',
            'show_profile_torrent_count' => 'required|boolean',
            'show_profile_torrent_extra' => 'required|boolean',
            'show_profile_torrent_ratio' => 'required|boolean',
            'show_profile_torrent_seed'  => 'required|boolean',
            'show_profile_warning'       => 'required|boolean',
            'show_requested'             => 'required|boolean',
            'show_topic'                 => 'required|boolean',
            'show_upload'                => 'required|boolean',
            'json_profile_groups'        => 'array',
            'json_profile_groups.*'      => Rule::in($validGroups),
            'json_torrent_groups'        => 'array',
            'json_torrent_groups.*'      => Rule::in($validGroups),
            'json_forum_groups'          => 'array',
            'json_forum_groups.*'        => Rule::in($validGroups),
            'json_bon_groups'            => 'array',
            'json_bon_groups.*'          => Rule::in($validGroups),
            'json_comment_groups'        => 'array',
            'json_comment_groups.*'      => Rule::in($validGroups),
            'json_wishlist_groups'       => 'array',
            'json_wishlist_groups.*'     => Rule::in($validGroups),
            'json_follower_groups'       => 'array',
            'json_follower_groups.*'     => Rule::in($validGroups),
            'json_achievement_groups'    => 'array',
            'json_achievement_groups.*'  => Rule::in($validGroups),
            'json_rank_groups'           => 'array',
            'json_rank_groups.*'         => Rule::in($validGroups),
            'json_request_groups'        => 'array',
            'json_request_groups.*'      => Rule::in($validGroups),
            'json_other_groups'          => 'array',
            'json_other_groups.*'        => Rule::in($validGroups),
            'private_profile'            => 'required|boolean',
            'hidden'                     => 'required|boolean',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    #[Override]
    protected function prepareForValidation(): void
    {
        $this->merge([
            'json_profile_groups'     => array_map(intval(...), $this->json_profile_groups ?? []),
            'json_torrent_groups'     => array_map(intval(...), $this->json_torrent_groups ?? []),
            'json_forum_groups'       => array_map(intval(...), $this->json_forum_groups ?? []),
            'json_bon_groups'         => array_map(intval(...), $this->json_bon_groups ?? []),
            'json_comment_groups'     => array_map(intval(...), $this->json_comment_groups ?? []),
            'json_wishlist_groups'    => array_map(intval(...), $this->json_wishlist_groups ?? []),
            'json_follower_groups'    => array_map(intval(...), $this->json_follower_groups ?? []),
            'json_achievement_groups' => array_map(intval(...), $this->json_achievement_groups ?? []),
            'json_rank_groups'        => array_map(intval(...), $this->json_rank_groups ?? []),
            'json_request_groups'     => array_map(intval(...), $this->json_request_groups ?? []),
            'json_other_groups'       => array_map(intval(...), $this->json_other_groups ?? []),
        ]);
    }
}

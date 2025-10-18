<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Helpers\Language;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGeneralSettingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'censor' => [
                'required',
                'boolean',
            ],
            'news_block_visible' => [
                'required',
                'boolean',
            ],
            'news_block_position' => [
                'required',
                'numeric',
            ],
            'chat_block_visible' => [
                'required',
                'boolean',
            ],
            'chat_block_position' => [
                'required',
                'numeric',
            ],
            'featured_block_visible' => [
                'required',
                'boolean',
            ],
            'featured_block_position' => [
                'required',
                'numeric',
            ],
            'random_media_block_visible' => [
                'required',
                'boolean',
            ],
            'random_media_block_position' => [
                'required',
                'numeric',
            ],
            'poll_block_visible' => [
                'required',
                'boolean',
            ],
            'poll_block_position' => [
                'required',
                'numeric',
            ],
            'top_torrents_block_visible' => [
                'required',
                'boolean',
            ],
            'top_torrents_block_position' => [
                'required',
                'numeric',
            ],
            'top_users_block_visible' => [
                'required',
                'boolean',
            ],
            'top_users_block_position' => [
                'required',
                'numeric',
            ],
            'latest_topics_block_visible' => [
                'required',
                'boolean',
            ],
            'latest_topics_block_position' => [
                'required',
                'numeric',
            ],
            'latest_posts_block_visible' => [
                'required',
                'boolean',
            ],
            'latest_posts_block_position' => [
                'required',
                'numeric',
            ],
            'latest_comments_block_visible' => [
                'required',
                'boolean',
            ],
            'latest_comments_block_position' => [
                'required',
                'numeric',
            ],
            'online_block_visible' => [
                'required',
                'boolean',
            ],
            'online_block_position' => [
                'required',
                'numeric',
            ],
            'locale' => [
                'required',
                Rule::in(array_keys(Language::allowed())),
            ],
            'style' => [
                'required',
                'numeric',
            ],
            'custom_css' => [
                'nullable',
                'url',
            ],
            'standalone_css' => [
                'nullable',
                'url',
            ],
            'torrent_layout' => [
                'required',
                Rule::in([0, 1, 2, 3]),
            ],
            'torrent_sort_field' => [
                'required',
                Rule::in(['created_at', 'bumped_at']),
            ],
            'torrent_search_autofocus' => [
                'required',
                'boolean',
            ],
            'show_poster' => [
                'required',
                'boolean',
            ],
            'unbookmark_torrents_on_completion' => [
                'required',
                'boolean',
            ],
            'show_adult_content' => [
                'required',
                'boolean',
            ],
        ];
    }
}

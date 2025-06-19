<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Language;
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
            'news_visible' => [
                'required',
                'boolean',
            ],
            'chat_visible' => [
                'required',
                'boolean',
            ],
            'featured_visible' => [
                'required',
                'boolean',
            ],
            'random_media_visible' => [
                'required',
                'boolean',
            ],
            'poll_visible' => [
                'required',
                'boolean',
            ],
            'top_torrents_visible' => [
                'required',
                'boolean',
            ],
            'top_users_visible' => [
                'required',
                'boolean',
            ],
            'latest_topics_visible' => [
                'required',
                'boolean',
            ],
            'latest_posts_visible' => [
                'required',
                'boolean',
            ],
            'latest_comments_visible' => [
                'required',
                'boolean',
            ],
            'online_visible' => [
                'required',
                'boolean',
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
        ];
    }
}

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
            'news_hidden' => [
                'required',
                'boolean',
            ],
            'chat_hidden' => [
                'required',
                'boolean',
            ],
            'featured_hidden' => [
                'required',
                'boolean',
            ],
            'random_media_hidden' => [
                'required',
                'boolean',
            ],
            'poll_hidden' => [
                'required',
                'boolean',
            ],
            'top_torrents_hidden' => [
                'required',
                'boolean',
            ],
            'top_users_hidden' => [
                'required',
                'boolean',
            ],
            'latest_topics_hidden' => [
                'required',
                'boolean',
            ],
            'latest_posts_hidden' => [
                'required',
                'boolean',
            ],
            'latest_comments_hidden' => [
                'required',
                'boolean',
            ],
            'online_hidden' => [
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

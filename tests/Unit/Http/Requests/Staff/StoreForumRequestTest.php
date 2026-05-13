<?php

declare(strict_types=1);

/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

use App\Http\Requests\Staff\StoreForumRequest;
use Illuminate\Validation\Rule;

beforeEach(function (): void {
    $this->subject = new StoreForumRequest();
});

test('authorize', function (): void {
    $actual = $this->subject->authorize();

    expect($actual)->toBeTrue();
});

test('rules', function (): void {
    $actual = $this->subject->rules();

    $this->assertValidationRules([
        'forum.name' => [
            'required',
        ],
        'forum.position' => [
            'required',
        ],
        'forum.slug' => [
            'required',
        ],
        'forum.description' => [
            'required',
        ],
        'forum.forum_category_id' => [
            'required',
            'exists:forum_categories,id',
        ],
        'forum.default_topic_state_filter' => [
            'sometimes',
            'nullable',
            Rule::in(['close', 'open', null]),
        ],
        'forum.is_invite_forum' => [
            'sometimes',
            'boolean',
        ],
        'permissions' => [
            'required',
            'array',
        ],
        'permissions.*' => [
            'required',
            'array:group_id,read_topic,reply_topic,start_topic',
        ],
        'permissions.*.group_id' => [
            'required',
            'exists:groups,id',
        ],
        'permissions.*.read_topic' => [
            'required',
            'boolean',
        ],
        'permissions.*.reply_topic' => [
            'required',
            'boolean',
        ],
        'permissions.*.start_topic' => [
            'required',
            'boolean',
        ],
    ], $actual);
});

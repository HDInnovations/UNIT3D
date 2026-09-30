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
 * @author     Roardom <roardom@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Controllers;

use App\Models\Forum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TopicReadController extends Controller
{
    /**
     * Update Topic.
     */
    public function update(Request $request): \Illuminate\Http\RedirectResponse
    {
        // Laravel Eloquent currently has no support for what should be
        // an `upsertUsing()` method, so we have to use raw SQL here
        switch ($request->string('catchup_type')) {
            case 'all':
                DB::insert('
                    INSERT INTO
                        topic_reads (user_id, topic_id, last_read_post_id)
                    SELECT
                        ?, id, last_post_id
                    FROM
                        topics
                    WHERE
                        (?, id, last_post_id) NOT IN (
                            SELECT
                                user_id,
                                topic_id,
                                last_read_post_id
                            FROM
                                topic_reads
                        )
                    AND
                        last_post_id IS NOT NULL
                    AND
                        EXISTS (
                            SELECT
                                *
                            FROM
                                forum_permissions
                            WHERE
                                topics.forum_id = forum_permissions.forum_id
                            AND
                                read_topic = 1
                            AND
                                group_id = ?
                        )
                    ON DUPLICATE KEY UPDATE
                        last_read_post_id = last_post_id
                ', [$request->user()->id, $request->user()->id, $request->user()->group_id]);

                return back()->with('success', __('application-messages.flash.caught-up'));
            case 'forum':
                $forum = Forum::authorized(canStartTopic: true)->findOrFail($request->integer('forum_id'));

                DB::insert('
                    INSERT INTO
                        topic_reads (user_id, topic_id, last_read_post_id)
                    SELECT
                        ?, id, last_post_id
                    FROM
                        topics
                    WHERE
                        (?, id, last_post_id) NOT IN (
                            SELECT
                                user_id,
                                topic_id,
                                last_read_post_id
                            FROM
                                topic_reads
                        )
                    AND
                        last_post_id IS NOT NULL
                    AND
                        forum_id = ?
                    ON DUPLICATE KEY UPDATE
                        last_read_post_id = last_post_id
                ', [$request->user()->id, $request->user()->id, $forum->id]);

                return to_route('forums.show', ['id' => $request->integer('forum_id')])
                    ->with('success', __('application-messages.flash.caught-up'));

            case 'forum_category':
                DB::insert('
                    INSERT INTO
                        topic_reads (user_id, topic_id, last_read_post_id)
                    SELECT
                        ?, topics.id, topics.last_post_id
                    FROM
                        topics
                    INNER JOIN
                        forums
                    ON
                        forums.id = topics.forum_id
                    WHERE
                        (?, topics.id, topics.last_post_id) NOT IN (
                            SELECT
                                user_id,
                                topic_id,
                                last_read_post_id
                            FROM
                                topic_reads
                        )
                    AND
                        topics.last_post_id IS NOT NULL
                    AND
                        EXISTS (
                            SELECT
                                *
                            FROM
                                forum_permissions
                            WHERE
                                topics.forum_id = forum_permissions.forum_id
                            AND
                                read_topic = 1
                            AND
                                group_id = ?
                        )
                    AND
                        forum_category_id = ?
                    ON DUPLICATE KEY UPDATE
                        last_read_post_id = last_post_id
                ', [
                    $request->user()->id,
                    $request->user()->id,
                    $request->user()->group_id,
                    $request->integer('forum_category_id')
                ]);

                return to_route('forums.categories.show', ['id' => $request->integer('forum_category_id')])
                    ->with('success', __('application-messages.flash.caught-up'));
            case 'subscriptions':
                DB::insert('
                    INSERT INTO
                        topic_reads (topic_id, user_id, last_read_post_id)
                    SELECT
                        ?, topics.id, topics.last_post_id
                    FROM
                        topics
                    INNER JOIN
                        subscriptions
                    ON
                        subscriptions.topic_id = topics.id
                    WHERE
                        subscriptions.user_id = ?
                    AND
                        (?, topics.id, topics.last_post_id) NOT IN (
                            SELECT
                                user_id,
                                topic_id,
                                last_read_post_id
                            FROM
                                topic_reads
                        )
                    AND
                        topics.last_post_id IS NOT NULL
                    AND
                        EXISTS (
                            SELECT
                                *
                            FROM
                                forum_permissions
                            WHERE
                                topics.forum_id = forum_permissions.forum_id
                            AND
                                read_topic = 1
                            AND
                                group_id = ?
                        )
                    ON
                        DUPLICATE KEY
                    UPDATE
                        last_read_post_id = last_post_id
                ', [
                    $request->user()->id,
                    $request->user()->id,
                    $request->user()->id,
                    $request->user()->group_id
                ]);

                return to_route('subscriptions.index')
                    ->with('success', __('application-messages.flash.caught-up'));
            default:
                return to_route('forums.index')
                    ->withErrors([__('application-messages.validation.catchup-failed')]);
        }
    }
}

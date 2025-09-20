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

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTopicLabelRequest;
use App\Models\Topic;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\TopicLabelControllerTest
 */
class TopicLabelController extends Controller
{
    /**
     * Apply/Remove Approved Label.
     */
    public function update(UpdateTopicLabelRequest $request, Topic $topic): \Illuminate\Http\RedirectResponse
    {
        $topic->update($request->validated());

        return to_route('topics.show', ['id' => $topic->id])
            ->with('info', 'Label change has been applied');
    }
}

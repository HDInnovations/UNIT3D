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

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Like;
use Dedoc\Scramble\Attributes\ExcludeAllRoutesFromDocs;

#[ExcludeAllRoutesFromDocs]
class LikeController extends Controller
{
    public function store(int $postId): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();
        $post = Post::findOrFail($postId);

        if ($user->id === $post->user_id) {
            abort(400, 'You cannot like your own post!');
        }

        if (Like::where('user_id', '=', $user->id)->where('post_id', '=', $post->id)->exists()) {
            abort(400, 'You have already liked or disliked this post!');
        }

        Like::create([
            'user_id' => $user->id,
            'post_id' => $post->id,
            'like'    => true,
        ]);

        return response()->json([
            'success'    => true,
            'likesCount' => $post->likes()->count(),
        ]);
    }
}

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

namespace App\Models;

use App\Helpers\Bbcode;
use App\Traits\Auditable;
use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use AllowDynamicProperties;

/**
 * App\Models\Wiki.
 *
 * @property int                             $id
 * @property string                          $name
 * @property string                          $content
 * @property int                             $category_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
#[AllowDynamicProperties]
final class Wiki extends Model
{
    use Auditable;

    protected $guarded = [];

    /**
     * Get the category associated with the wiki.
     *
     * @return BelongsTo<WikiCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(WikiCategory::class);
    }

    /**
     * Parse content and get valid HTML.
     */
    public function getContentHtml(): string
    {
        $content = $this->replaceCurrentUserPlaceholders($this->content);

        return Markdown::convert(htmlspecialchars_decode((new Bbcode())->parse($content, false), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5))->getContent();
    }

    private function replaceCurrentUserPlaceholders(string $content): string
    {
        $user = Auth::user();

        if ($user === null) {
            return $content;
        }

        return str_replace(
            ['{user}', '{username}', '{user_id}'],
            [$user->username, $user->username, (string) $user->id],
            $content,
        );
    }
}

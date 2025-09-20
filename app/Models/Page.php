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
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Page.
 *
 * @property int                             $id
 * @property string|null                     $name
 * @property string|null                     $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Page extends Model
{
    use Auditable;

    /** @use HasFactory<\Database\Factories\PageFactory> */
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Set the pages content after it has been purified.
     */
    public function setContentAttribute(?string $value): void
    {
        $this->attributes['content'] = $value;
    }

    /**
     * Parse content and return valid HTML.
     */
    public function getContentHtml(): string
    {
        return Markdown::convert(htmlspecialchars_decode((new Bbcode())->parse($this->content, false), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5))->getContent();
    }
}

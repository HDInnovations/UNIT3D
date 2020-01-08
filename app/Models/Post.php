<?php
/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 *
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 * @author     HDVinnie
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Helpers\Bbcode;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Post.
 *
 * @property int $id
 * @property string $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $user_id
 * @property int $topic_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Like[] $likes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BonTransactions[] $tips
 * @property-read \App\Models\Topic $topic
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post whereTopicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Post whereUserId($value)
 * @mixin \Eloquent
 *
 * @property-read int|null $likes_count
 * @property-read int|null $tips_count
 */
class Post extends Model
{
    use Auditable;

    /**
     * Belongs To A Topic.
     *
     * @return BelongsTo
     */
    public function topic(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Belongs To A User.
     *
     * @return BelongsTo
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'username' => 'System',
            'id'       => '1',
        ]);
    }

    /**
     * A Post Has Many Likes.
     *
     * @return HasMany
     */
    public function likes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Like::class);
    }

    /**
     * A Post Has Many Tips.
     *
     * @return HasMany
     */
    public function tips(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BonTransactions::class);
    }

    /**
     * Set The Posts Content After Its Been Purified.
     *
     * @param string $value
     *
     * @return void
     */
    public function setContentAttribute(string $value): void
    {
        $this->attributes['content'] = htmlspecialchars($value);
    }

    /**
     * Parse Content And Return Valid HTML.
     *
     * @return string Parsed BBCODE To HTML
     */
    public function getContentHtml(): string
    {
        $bbcode = new Bbcode();

        return $bbcode->parse($this->content, true);
    }

    /**
     * Post Trimming.
     *
     * @param $length
     * @param $ellipses
     * @param $strip_html
     *
     * @return string Formatted And Trimmed Content
     */
    public function getBrief($length = 100, $ellipses = true, $strip_html = false): string
    {
        $input = $this->content;
        //strip tags, if desired
        if ($strip_html) {
            $input = strip_tags($input);
        }

        //no need to trim, already shorter than trim length
        if (strlen($input) <= $length) {
            return $input;
        }

        //find last space within length
        $last_space = strrpos(substr($input, 0, $length), ' ');
        $trimmed_text = substr($input, 0, $last_space);

        //add ellipses (...)
        if ($ellipses) {
            $trimmed_text .= '...';
        }

        return $trimmed_text;
    }

    /**
     * Get A Post From A ID.
     *
     * @return string
     */
    public function getPostNumber(): string
    {
        return $this->topic->postNumberFromId($this->id);
    }

    /**
     * Get A Posts Page Number.
     *
     * @return string
     */
    public function getPageNumber(): float
    {
        $result = ($this->getPostNumber() - 1) / 25 + 1;

        return floor($result);
    }
}

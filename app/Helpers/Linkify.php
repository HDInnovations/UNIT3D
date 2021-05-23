<?php
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

namespace App\Helpers;

use VStelmakh\UrlHighlight\UrlHighlight;

class Linkify
{
    /**
     * @var string
     */
    public function linky($text)
    {
        $urlHighlight = new UrlHighlight();

        return \str_replace('a href=', 'a rel="noopener noreferrer" href=', $urlHighlight->highlightUrls($text));
    }
}

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

use App\Helpers\Bbcode;
use App\Helpers\Linkify;

test('bbcode url links open in a new tab', function (): void {
    $html = (new Bbcode())->parse('[url=https://example.com]Example[/url]');

    expect($html)->toBe('<a href="https://example.com" target="_blank" rel="noopener noreferrer">Example</a>');
});

test('auto-linked urls open in a new tab', function (): void {
    $html = (new Linkify())->linky('Visit https://example.com');

    expect($html)
        ->toContain('href="https://example.com"')
        ->toContain('target="_blank"')
        ->toContain('rel="noopener noreferrer"');
});

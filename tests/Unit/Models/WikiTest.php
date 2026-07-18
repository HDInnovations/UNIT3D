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

use App\Models\Wiki;

describe('wiki model', function (): void {
    it('parses BBCode and Markdown', function (): void {
        $wiki = new Wiki([
            'content' => '# Hello [b]world[/b]',
        ]);

        expect(trim($wiki->getContentHtml()))->toBe('<h1>Hello <b>world</b></h1>');
    });

    it('escapes raw html in content', function (): void {
        $wiki = new Wiki([
            'content' => '<script>alert(1)</script>',
        ]);

        expect($wiki->getContentHtml())
            ->not->toContain('<script>')
            ->toContain('&lt;script&gt;');
    });

    it('neutralizes unsafe markdown links', function (): void {
        $wiki = new Wiki([
            'content' => '[click](javascript:alert(1))',
        ]);

        expect($wiki->getContentHtml())->not->toContain('javascript:');
    });
});

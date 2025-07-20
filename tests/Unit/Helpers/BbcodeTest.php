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

use App\Helpers\Bbcode;

describe('markdown support', tests: function (): void {
    it(
        'Generates HTML from BBCode',
        function (string $result, string $bbcode): void {
            $html = new Bbcode()->parse($bbcode, replaceLineBreaks: true);

            expect($html)->toBe($result);
        }
    )
        ->with(function (): iterable {
            yield from bbcodeBasic();

            yield from bbcodeImplicitLists();

            yield from bbcodeCodeBlocks();

            yield from bbcodeBlockQuote();
        });
});

function bbcodeBasic(): iterable
{
    yield 'heading' => ['<h1>Heading</h1>', '[h1]Heading[/h1]'];

    yield 'table' => ['<table><tr><th>Header</th></tr><tr><td>Cell</td></tr></table>', '[table][tr][th]Header[/th][/tr][tr][td]Cell[/td][/tr][/table]'];

    yield 'list' => ['<ul><li>Item 1<li>Item 2</ul>', "[list]\n[*]Item 1\n[*]Item 2\n[/list]"];

    yield 'image' => ['<img src="https://wsrv.nl/?n=-1&ll&url=https%3A%2F%2Fexample.com%2Fimage.jpg" loading="lazy" class="img-responsive" style="display: inline !important;">', '[img]https://example.com/image.jpg[/img]'];

    yield 'link' => ['<a href="https://example.com">Link</a>', '[url=https://example.com]Link[/url]'];

    yield 'inline link' => ['<a href="https://example.com">https://example.com</a>', '[url]https://example.com[/url]'];

    yield 'bold' => ['<b>This is bold</b>', '[b]This is bold[/b]'];

    yield 'italic' => ['<i>This is italic</i>', '[i]This is italic[/i]'];

    yield 'strikethrough' => ['<s>This is strikethrough</s>', '[s]This is strikethrough[/s]'];

    yield 'bold italic' => ['<i><b>This is bold italic</b></i>', '[i][b]This is bold italic[/b][/i]'];

    yield 'bold italic strikethrough' => ['<i><b><s>This is bold italic strikethrough</s></b></i>', '[i][b][s]This is bold italic strikethrough[/s][/b][/i]'];

    yield 'code' => ['Do not use <code>dump()</code> in your code please.', 'Do not use [pre]dump()[/pre] in your code please.'];

    yield 'heading level 1' => ['<h1>Header 1</h1><h2>Header 2</h2><h3>Header 3</h3><h4>Header 4</h4><h5>Header 5</h5><h6>Header 6</h6>', "[h1]Header 1[/h1]\n\n[h2]Header 2[/h2]\n\n[h3]Header 3[/h3]\n\n[h4]Header 4[/h4]\n\n[h5]Header 5[/h5]\n\n[h6]Header 6[/h6]"];

    yield 'horizontal rule' => ['<hr>', '[hr]'];

    yield 'escaped html' => ['&lt;test', '<test'];

    yield 'list with nested list' => [
        '<ul>'
        .'<li>Item 1'
        .'<ul>'
        .'<li>Item 1.1'
        .'<br>Test'
        .'<li>Item 1.2'
        .'<ul>'
        .'<li>Item 1.2.1'
        .'</ul>'
        .'</ul>'
        .'<li>Item 2'
        .'<li>Item 3'
        .'</ul>'
        .'Test',
        <<<BBCode
        [list]
        [*]Item 1
        [list][*]Item 1.1
        Test
        [*]Item 1.2
        [list]
        [*]Item 1.2.1
        [/list]
        [/list]
        [*]Item 2
        [*]Item 3
        [/list]
        Test
        BBCode,
    ];

    yield 'ordered list' => ['<ol><li>Item 1<li>Item 2</ol>', "[list=1][*]Item 1\n[*]Item 2\n[/list]"];

    yield 'html elements with safe mode' => ['&lt;strong&gt;Strong&lt;/strong&gt;', '<strong>Strong</strong>'];
}

function bbcodeImplicitLists(): iterable
{
    yield 'simple implicit list' => [
        '<ul><li>Item 1<li>Item 2</ul>',
        <<<'BBCode'
        [*]Item 1
        [*]Item 2
        BBCode,
    ];

    yield '2 simple implicit lists' => [
        '<ul><li>Item 1.1<li>Item 1.2</ul><br><br><ul><li>Item 2.1<li>Item 2.2</ul>',
        <<<'BBCode'
        [*]Item 1.1
        [*]Item 1.2

        [*]Item 2.1
        [*]Item 2.2
        BBCode,
    ];

    yield 'bolded simple implicit list' => [
        '<b><br><ul><li>Item 1<li>Item 2</ul></b>',
        <<<'BBCode'
        [b]
        [*]Item 1
        [*]Item 2
        [/b]
        BBCode,
    ];

    yield 'correct strike-through implicit list item' => [
        '<ul><li>Item 1<li><s>Item 2</s><li>Item 3</ul>',
        <<<'BBCode'
        [*]Item 1
        [*][s]Item 2[/s]
        [*]Item 3
        BBCode,
    ];

    yield 'incorrect strike-through implicit list item' => [
        '<ul><li>Item 1<br><s><ul><li>Item 2</ul></s><li>Item 3</ul>',
        <<<'BBCode'
        [*]Item 1
        [s][*]Item 2[/s]
        [*]Item 3
        BBCode,
    ];
}

function bbcodeCodeBlocks(): iterable
{
    yield 'code block' => [
        '<pre>echo &quot;Hello, World!&quot;;</pre>',
        "[code]\necho \"Hello, World!\";\n[/code]",
    ];

    yield 'code block with bbcode inside' => [
        '<pre>'
        .'code();<br>'
        .'[url]https://example.com[/url]'
        .'</pre>',
        <<<'BBCode'
        [code]
        code();
        [url]https://example.com[/url]
        [/code]
        BBCode,
        false,
    ];
}

function bbcodeBlockQuote(): iterable
{
    yield 'blockquote' => [
        '<blockquote><i class="fas fa-quote-left"></i> <cite>Quoting @username:</cite><p>'
            .'This is a quote'
            .'<blockquote><i class="fas fa-quote-left"></i> <cite>Quoting username2:</cite><p>'
            .'This is an inner quote'
            .'<br>And here is the second line.'
            .'</p></blockquote>'
            .'</p></blockquote>'
            .'And this is not a quote',
        <<<'BBCode'
        [quote=@username]
        This is a quote
        [quote=username2]
        This is an inner quote
        And here is the second line.
        [/quote]
        [/quote]

        And this is not a quote
        BBCode,
    ];
}

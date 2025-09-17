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

namespace App\Helpers;

use App\Models\WhitelistedImageUrl;

class Bbcode
{
    /**
     * @var array<
     *     string,
     *     array{
     *         openBbcode: string,
     *         closeBbcode: string,
     *         openHtml: string,
     *         closeHtml: string,
     *         block: boolean
     *     }
     * > $parsers.
     */
    private array $parsers = [
        'h1' => [
            'openBbcode'  => '/^\[h1\]/i',
            'closeBbcode' => '[/h1]',
            'openHtml'    => '<h1>',
            'closeHtml'   => '</h1>',
            'block'       => true,
        ],
        'h2' => [
            'openBbcode'  => '/^\[h2\]/i',
            'closeBbcode' => '[/h2]',
            'openHtml'    => '<h2>',
            'closeHtml'   => '</h2>',
            'block'       => true,
        ],
        'h3' => [
            'openBbcode'  => '/^\[h3\]/i',
            'closeBbcode' => '[/h3]',
            'openHtml'    => '<h3>',
            'closeHtml'   => '</h3>',
            'block'       => true,
        ],
        'h4' => [
            'openBbcode'  => '/^\[h4\]/i',
            'closeBbcode' => '[/h4]',
            'openHtml'    => '<h4>',
            'closeHtml'   => '</h4>',
            'block'       => true,
        ],
        'h5' => [
            'openBbcode'  => '/^\[h5\]/i',
            'closeBbcode' => '[/h5]',
            'openHtml'    => '<h5>',
            'closeHtml'   => '</h5>',
            'block'       => true,
        ],
        'h6' => [
            'openBbcode'  => '/^\[h6\]/i',
            'closeBbcode' => '[/h6]',
            'openHtml'    => '<h6>',
            'closeHtml'   => '</h6>',
            'block'       => true,
        ],
        'bold' => [
            'openBbcode'  => '/^\[b\]/i',
            'closeBbcode' => '[/b]',
            'openHtml'    => '<b>',
            'closeHtml'   => '</b>',
            'block'       => false,
        ],
        'italic' => [
            'openBbcode'  => '/^\[i\]/i',
            'closeBbcode' => '[/i]',
            'openHtml'    => '<i>',
            'closeHtml'   => '</i>',
            'block'       => false,
        ],
        'underline' => [
            'openBbcode'  => '/^\[u\]/i',
            'closeBbcode' => '[/u]',
            'openHtml'    => '<u>',
            'closeHtml'   => '</u>',
            'block'       => false,
        ],
        'linethrough' => [
            'openBbcode'  => '/^\[s\]/i',
            'closeBbcode' => '[/s]',
            'openHtml'    => '<s>',
            'closeHtml'   => '</s>',
            'block'       => false,
        ],
        'size' => [
            'openBbcode'  => '/^\[size=(\d+)\]/i',
            'closeBbcode' => '[/size]',
            'openHtml'    => '<span style="font-size: clamp(10px, $1px, 100px);">',
            'closeHtml'   => '</span>',
            'block'       => false,
        ],
        'font' => [
            'openBbcode'  => '/^\[font=([a-z0-9 ]+)\]/i',
            'closeBbcode' => '[/font]',
            'openHtml'    => '<span style="font-family: $1;">',
            'closeHtml'   => '</span>',
            'block'       => false,
        ],
        'color' => [
            'openBbcode'  => '/^\[color=(\#[a-f0-9]{3,4}|\#[a-f0-9]{6}|\#[a-f0-9]{8}|[a-z]+)\]/i',
            'closeBbcode' => '[/color]',
            'openHtml'    => '<span style="color: $1;">',
            'closeHtml'   => '</span>',
            'block'       => false,
        ],
        'center' => [
            'openBbcode'  => '/^\[center\]/i',
            'closeBbcode' => '[/center]',
            'openHtml'    => '<div class="bbcode-rendered__center" style="text-align: center;">',
            'closeHtml'   => '</div>',
            'block'       => true,
        ],
        'left' => [
            'openBbcode'  => '/^\[left\]/i',
            'closeBbcode' => '[/left]',
            'openHtml'    => '<div class="bbcode-rendered__left" style="text-align: left;">',
            'closeHtml'   => '</div>',
            'block'       => true,
        ],
        'right' => [
            'openBbcode'  => '/^\[right\]/i',
            'closeBbcode' => '[/right]',
            'openHtml'    => '<div class="bbcode-rendered__right" style="text-align: right;">',
            'closeHtml'   => '</div>',
            'block'       => true,
        ],
        'quote' => [
            'openBbcode'  => '/^\[quote\]/i',
            'closeBbcode' => '[/quote]',
            'openHtml'    => '<blockquote>',
            'closeHtml'   => '</blockquote>',
            'block'       => true,
        ],
        'namedquote' => [
            'openBbcode'  => '/^\[quote=(.*?)\]/i',
            'closeBbcode' => '[/quote]',
            'openHtml'    => '<blockquote><i class="fas fa-quote-left"></i> <cite>Quoting $1:</cite><p>',
            'closeHtml'   => '</p></blockquote>',
            'block'       => true,
        ],
        'orderedlistnumerical' => [
            'openBbcode'  => '/^\[list=1\]/i',
            'closeBbcode' => '[/list]',
            'openHtml'    => '<ol>',
            'closeHtml'   => '</ol>',
            'block'       => true,
        ],
        'orderedlistalpha' => [
            'openBbcode'  => '/^\[list=a\]/i',
            'closeBbcode' => '[/list]',
            'openHtml'    => '<ol type="a">',
            'closeHtml'   => '</ol>',
            'block'       => true,
        ],
        'unorderedlist' => [
            'openBbcode'  => '/^\[list\]/i',
            'closeBbcode' => '[/list]',
            'openHtml'    => '<ul>',
            'closeHtml'   => '</ul>',
            'block'       => true,
        ],
        'code' => [
            'openBbcode'  => '/^\[code\]/i',
            'closeBbcode' => '[/code]',
            'openHtml'    => '<div class="bbcode-rendered__clipboard" x-data="clipboardButton"><pre><code>',
            'closeHtml'   => '</code></pre><div class="bbcode-rendered__clipboard-container"><button class="bbcode-rendered__clipboard-button" x-bind="button"><i class="fa fa-clone"></i></button></div></div>',
            'block'       => true,
        ],
        'pre' => [
            'openBbcode'  => '/^\[pre\]/i',
            'closeBbcode' => '[/pre]',
            'openHtml'    => '<code>',
            'closeHtml'   => '</code>',
            'block'       => false,
        ],
        'alert' => [
            'openBbcode'  => '/^\[alert\]/i',
            'closeBbcode' => '[/alert]',
            'openHtml'    => '<div class="bbcode-rendered__alert">',
            'closeHtml'   => '</div>',
            'block'       => true,
        ],
        'note' => [
            'openBbcode'  => '/^\[note\]/i',
            'closeBbcode' => '[/note]',
            'openHtml'    => '<div class="bbcode-rendered__note">',
            'closeHtml'   => '</div>',
            'block'       => true,
        ],
        'sub' => [
            'openBbcode'  => '/^\[sub\]/i',
            'closeBbcode' => '[/sub]',
            'openHtml'    => '<sub>',
            'closeHtml'   => '</sub>',
            'block'       => false,
        ],
        'sup' => [
            'openBbcode'  => '/^\[sup\]/i',
            'closeBbcode' => '[/sup]',
            'openHtml'    => '<sup>',
            'closeHtml'   => '</sup>',
            'block'       => false,
        ],
        'small' => [
            'openBbcode'  => '/^\[small\]/i',
            'closeBbcode' => '[/small]',
            'openHtml'    => '<small>',
            'closeHtml'   => '</small>',
            'block'       => false,
        ],
        'table' => [
            'openBbcode'  => '/^\[table\]/i',
            'closeBbcode' => '[/table]',
            'openHtml'    => '<table>',
            'closeHtml'   => '</table>',
            'block'       => true,
        ],
        'table-row' => [
            'openBbcode'  => '/^\[tr\]/i',
            'closeBbcode' => '[/tr]',
            'openHtml'    => '<tr>',
            'closeHtml'   => '</tr>',
            'block'       => true,
        ],
        'table-header' => [
            'openBbcode'  => '/^\[th\]/i',
            'closeBbcode' => '[/th]',
            'openHtml'    => '<th>',
            'closeHtml'   => '</th>',
            'block'       => true,
        ],
        'table-data' => [
            'openBbcode'  => '/^\[td\]/i',
            'closeBbcode' => '[/td]',
            'openHtml'    => '<td>',
            'closeHtml'   => '</td>',
            'block'       => true,
        ],
        'spoiler' => [
            'openBbcode'  => '/^\[spoiler\]/i',
            'closeBbcode' => '[/spoiler]',
            'openHtml'    => '<details><summary>Spoiler</summary><div style="text-align:left;">',
            'closeHtml'   => '</div></details>',
            'block'       => false,
        ],
        'named-spoiler' => [
            'openBbcode'  => '/^\[spoiler=(.*?)\]/i',
            'closeBbcode' => '[/spoiler]',
            'openHtml'    => '<details><summary>$1</summary><div style="text-align:left;">',
            'closeHtml'   => '</div></details>',
            'block'       => false,
        ],
    ];

    /**
     * Parses the BBCode string.
     */
    public function parse(?string $source, bool $replaceLineBreaks = true): string
    {
        $source ??= '';
        $source = htmlspecialchars($source, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');

        // Replace all void elements since they don't have closing tags
        $source = str_replace('[*]', '<li>', (string) $source);
        $source = str_replace('[hr]', '<hr>', $source);
        $source = preg_replace_callback(
            '/\[url](.*?)\[\/url]/i',
            fn ($matches) => '<a href="'.$this->sanitizeUrl($matches[1]).'">'.$this->sanitizeUrl($matches[1]).'</a>',
            $source
        );
        $source = preg_replace_callback(
            '/\[url=(.*?)](.*?)\[\/url]/i',
            fn ($matches) => '<a href="'.$this->sanitizeUrl($matches[1]).'">'.$matches[2].'</a>',
            $source ?? ''
        );
        $source = preg_replace_callback(
            '/\[img](.*?)\[\/img]/i',
            fn ($matches) => '<img src="'.$this->sanitizeUrl($matches[1], isImage: true).'" loading="lazy" class="img-responsive" style="display: inline !important;">',
            $source ?? ''
        );
        $source = preg_replace_callback(
            '/\[img width=(\d+)](.*?)\[\/img]/i',
            fn ($matches) => '<img src="'.$this->sanitizeUrl($matches[2], isImage: true).'" loading="lazy" width="'.$matches[1].'px">',
            $source ?? ''
        );
        $source = preg_replace_callback(
            '/\[img=(\d+)(?:x\d+)?](.*?)\[\/img]/i',
            fn ($matches) => '<img src="'.$this->sanitizeUrl($matches[2], isImage: true).'" loading="lazy" width="'.$matches[1].'px">',
            $source ?? ''
        );

        // YouTube video elements need to be replaced like this because the content inside the two tags
        // has to be moved into an HTML attribute
        $source = preg_replace_callback(
            '/\[youtube]([a-z0-9_-]{11})\[\/youtube]/i',
            static fn ($matches) => '<iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/'.$matches[1].'?rel=0" allow="autoplay; encrypted-media" allowfullscreen></iframe>',
            $source ?? ''
        );
        $source = preg_replace_callback(
            '/\[video]([a-z0-9_-]{11})\[\/video]/i',
            static fn ($matches) => '<iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/'.$matches[1].'?rel=0" allow="autoplay; encrypted-media" allowfullscreen></iframe>',
            $source ?? ''
        );
        $source = preg_replace_callback(
            '/\[video=&quot;youtube&quot;]([a-z0-9_-]{11})\[\/video]/i',
            static fn ($matches) => '<iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/'.$matches[1].'?rel=0" allow="autoplay; encrypted-media" allowfullscreen></iframe>',
            $source ?? ''
        );

        // Common comparison syntax used in other torrent management systems is quite specific,
        // so it must be done here instead
        $source = preg_replace_callback(
            '/\[comparison=(.*?)]\s*(.*?)\s*\[\/comparison]/is',
            function ($matches) {
                $comparates = preg_split('/\s*,\s*/', $matches[1]);
                $urls = preg_split('/\s*(?:,|\s)\s*/', $matches[2]);

                if ($comparates === false || $urls === false) {
                    return 'Broken comparison';
                }

                $validatedUrls = collect($urls)->map(fn ($url) => $this->sanitizeUrl($url, isImage: true));
                $chunkedUrls = $validatedUrls->chunk(\count($comparates));
                $html = view('partials.comparison', ['comparates' => $comparates, 'urls' => $chunkedUrls])->render();
                $html = preg_replace('/\s+/', ' ', $html);

                if (!\is_string($html)) {
                    return 'Broken html';
                }

                return $html;
            },
            $source ?? ''
        );

        // Stack of unclosed elements
        $openedElements = [];

        // Character index
        $index = 0;

        // Don't loop more than the length of the source
        while ($index < \strlen((string) $source)) {
            // Get the next occurrence of `[`
            $index = strpos((string) $source, '[', $index);

            // Break if there are no more occurrences of `[`
            if ($index === false) {
                break;
            }

            // Break if `[` is the last character of the source
            if ($index + 1 >= \strlen((string) $source)) {
                break;
            }

            // Is the potential tag opening or closing?
            if ($source[$index + 1] === '/' && !empty($openedElements)) {
                $name = array_pop($openedElements);
                $el = $this->parsers[$name];
                $tag = substr((string) $source, $index, \strlen((string) $el['closeBbcode']));

                // Replace bbcode tag with html tag if found tag matches expected tag,
                // otherwise return the expected element's to the stack
                if (strcasecmp($tag, (string) $el['closeBbcode']) === 0) {
                    $source = substr_replace((string) $source, (string) $el['closeHtml'], $index, \strlen((string) $el['closeBbcode']));

                    if ($replaceLineBreaks === true && $el['block'] === true) {
                        $this->handleBlockElementSpacing($source, $index, $index, $index + \strlen((string) $el['closeHtml']) - 1);
                    }
                } else {
                    $openedElements[] = $name;
                }
            } else {
                $remainingText = substr((string) $source, $index);

                // Find match between found bbcode tag and valid elements
                foreach ($this->parsers as $name => $el) {
                    // The opening bbcode tag uses the regex `^` character to make
                    // sure only the beginning of $remainingText is matched
                    if (preg_match($el['openBbcode'], $remainingText, $matches) === 1) {
                        $replacement = (string) preg_replace($el['openBbcode'], (string) $el['openHtml'], $matches[0]);
                        $source = substr_replace((string) $source, $replacement, $index, \strlen($matches[0]));

                        if ($replaceLineBreaks === true && $el['block'] === true) {
                            $this->handleBlockElementSpacing($source, $index, $index, $index + \strlen($replacement) - 1);
                        }

                        $openedElements[] = $name;

                        break;
                    }
                }
            }

            $index++;
        }

        while (!empty($openedElements)) {
            $source .= $this->parsers[array_pop($openedElements)]['closeHtml'];
        }

        if ($replaceLineBreaks) {
            // Replace line breaks
            $source = str_replace(["\r\n", "\n"], '<br>', (string) $source);
        }

        return $source;
    }

    /**
     * Remove line breaks immediately before and after tags for block elements.
     *
     * Useful so that you can write bbcode like the following without worrying about spacing:
     *
     * ```
     * [list]
     * [*]item 1
     * [*]item 2
     * [/list]
     * ```
     *
     * @param string $source        Reference to the source text content currently being converted from bbcode to html.
     * @param int    $index         Reference to the current index of `$source` that the parser must keep track of.
     * @param int    $tagStartIndex The index of the first character of the tag being parsed inside `$source`. Should be the `[` character.
     * @param int    $tagStopIndex  The index of the last character of the tag being parsed inside `$source`. Should be the `]` character.
     */
    private function handleBlockElementSpacing(string &$source, int &$index, int $tagStartIndex, int $tagStopIndex): void
    {
        // Remove two line breaks (if they exist) instead of one, since a
        // line break after a block element is positioned on the line after
        // the block element, while a line break after a non-block element
        // is positioned at the end of the same line of the non-block element.
        // I.e. two line breaks after a block element provides the same amount
        // of vertical space as one line break after a non-block element.
        for ($i = 0; $i < 2; $i++) {
            $bbcodeStopIndex = \strlen($source) - 1;

            // Does there exist 2 characters after the tag and are they \r\n?
            // Otherwise, does there exist 1 character after the tag and is it \n?
            // In either case, remove those characters.
            if ($tagStopIndex + 2 <= $bbcodeStopIndex && substr_compare($source, "\r\n", $tagStopIndex + 1, 2) === 0) {
                $source = substr_replace($source, '', $tagStopIndex + 1, 2);
            } elseif ($tagStopIndex + 1 <= $bbcodeStopIndex && $source[$tagStopIndex + 1] === "\n") {
                $source = substr_replace($source, '', $tagStopIndex + 1, 1);
            }
        }

        // Does there exist 2 characters before the tag and are they \r\n?
        // Otherwise, does there exist 1 character before the tag and is it \n?
        // In either case, remove those characters and adjust the current index appropriately.
        if ($tagStartIndex >= 2 && substr_compare($source, "\r\n", $tagStartIndex - 2, 2) === 0) {
            $source = substr_replace($source, '', $tagStartIndex - 2, 2);
            $index -= 2;
        } elseif ($tagStartIndex >= 1 && $source[$tagStartIndex - 1] === "\n") {
            $source = substr_replace($source, '', $tagStartIndex - 1, 1);
            $index -= 1;
        }
    }

    private function sanitizeUrl(string $url, ?bool $isImage = null): string
    {
        // Do NOT add `javascript`, `data` or `vbscript` here
        // or else you will allow an XSS vulnerability!
        $protocolWhitelist = [
            'http',
            'https',
            'irc',
            'ftp',
            'sftp',
            'magnet',
        ];

        if (str_starts_with($url, '/')) {
            $url = config('app.url').'/'.ltrim($url, '/');
        } elseif (!\in_array(parse_url($url, PHP_URL_SCHEME), $protocolWhitelist)) {
            $url = 'https://'.$url;
        }

        if (false === filter_var($url, FILTER_VALIDATE_URL)) {
            return 'Broken link';
        }

        if ($isImage) {
            $whitelistedImageUrls = cache()->rememberForever(
                'whitelisted-image-urls',
                fn () => WhitelistedImageUrl::query()->pluck('pattern'),
            );

            $isWhitelisted = $whitelistedImageUrls->contains(function (string $pattern) use ($url) {
                $pattern = str_replace(['\*\*', '\*'], ['.*', '[^\/.]*'], preg_quote($pattern, '/'));

                return preg_match('/^'.$pattern.'$/i', $url) === 1;
            });

            if (!$isWhitelisted) {
                $url = 'https://wsrv.nl/?n=-1&ll&url='.urlencode($url);
            }
        }

        return $url;
    }
}

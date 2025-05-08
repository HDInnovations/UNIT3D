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

namespace App\Helpers;

use function theodorejb\polycast\safe_int;

class Bencode
{
    public static function parse_integer($s, &$pos)
    {
        $len = \strlen((string) $s);

        if ($len === 0 || $s[$pos] != 'i') {
            return;
        }

        $pos++;

        $result = '';

        while ($pos < $len && $s[$pos] != 'e') {
            if (is_numeric($s[$pos]) || $s[$pos] === '-') {
                $result .= $s[$pos];
            } else {
                // We have an invalid character in the string.
                return;
            }

            $pos++;
        }

        if ($pos >= $len) {
            // No end marker, hence we return null.
            return;
        }

        $pos++;

        if (safe_int($result)) {
            return (int) $result;
        }
    }

    public static function parse_string($s, &$pos)
    {
        $len = \strlen((string) $s);
        $lengthStr = '';

        while ($pos < $len && $s[$pos] != ':') {
            if (is_numeric($s[$pos])) {
                $lengthStr .= $s[$pos];
            } else {
                // Non-numeric character, we return null in this case.
                return;
            }

            $pos++;
        }

        if ($pos >= $len) {
            // We need a colon here, but there's none.
            return;
        }

        $pos++;

        if (!safe_int($lengthStr)) {
            return;
        }

        $length = (int) $lengthStr;
        $result = '';

        while ($pos < $len && $length > 0) {
            $result .= $s[$pos];
            $length--;
            $pos++;
        }

        if ($length > 0) {
            // Input ended, but the string is longer than that.
            return;
        }

        return $result;
    }

    public static function bdecode($s, &$pos = 0)
    {
        $len = \strlen((string) $s);

        if ($pos >= $len) {
            return;
        }

        $c = $s[$pos];

        if ($c == 'i') {
            return self::parse_integer($s, $pos);
        }

        if (is_numeric($c)) {
            return self::parse_string($s, $pos);
        }

        if ($c == 'd') {
            $dict = [];
            $pos++;

            while ($pos < $len && $s[$pos] != 'e') {
                $key = self::bdecode($s, $pos);
                $value = self::bdecode($s, $pos);

                if (null === $key || null === $value) {
                    return;
                }

                $dict[$key] = $value;
            }

            if ($pos >= $len) {
                // We need a end marker here
                return;
            }

            $pos++;

            return $dict;
        }

        if ($c == 'l') {
            $list = [];
            $pos++;

            while ($pos < $len && $s[$pos] != 'e') {
                $next = self::bdecode($s, $pos);

                if (null !== $next) {
                    $list[] = $next;
                } else {
                    return;
                }
            }

            if ($pos >= $len) {
                // We need a end marker here
                return;
            }

            $pos++;

            return $list;
        }
    }

    public static function bencode($d)
    {
        if (\is_array($d)) {
            $ret = 'l';
            $isDict = !array_is_list($d);

            if ($isDict) {
                $ret = 'd';
                // this is required by the specs, and BitTornado actually chokes on unsorted dictionaries
                ksort($d, SORT_STRING);
            }

            foreach ($d as $key => $value) {
                if ($isDict) {
                    $ret .= \strlen((string) $key).':'.$key;
                }

                if (\is_int($value) || \is_float($value)) {
                    $ret .= \sprintf('i%de', $value);
                } elseif (\is_string($value)) {
                    $ret .= \strlen($value).':'.$value;
                } else {
                    $ret .= self::bencode($value);
                }
            }

            return $ret.'e';
        }

        if (\is_string($d)) {
            return \strlen($d).':'.$d;
        }

        if (\is_int($d) || \is_float($d)) {
            return \sprintf('i%de', $d);
        }
    }

    public static function bdecode_file($filename)
    {
        $f = file_get_contents($filename);

        return self::bdecode($f);
    }

    public static function get_infohash($t): string
    {
        return sha1((string) self::bencode($t['info']), true);
    }

    public static function get_meta($t): array
    {
        $result = [];
        $size = 0;
        $count = 0;

        // Multi-file
        if (isset($t['info']['files']) && \is_array($t['info']['files'])) {
            foreach ($t['info']['files'] as $file) {
                $count++;
                $size += $file['length'];
            }
        } else {
            $size = $t['info']['length'];
            $count = 1;
            $t['info']['files'][0]['path'] = $t['info']['name'];
            $t['info']['files'][0]['length'] = $t['info']['length'];
        }

        $result['count'] = $count;
        $result['size'] = $size;

        if (isset($t['info']['piece length'])) {
            $result['piece_length'] = $t['info']['piece length'];
        }

        return $result;
    }

    public static function get_name($t): ?string
    {
        $name = null;

        if (
            \array_key_exists('info', $t)
            && \is_array($t['info'])
            && \array_key_exists('name', $t['info'])
            && \is_string($t['info']['name'])
            && \array_key_exists('files', $t['info'])
        ) {
            $name = $t['info']['name'];
        }

        return $name;
    }

    public static function is_v2_or_hybrid($t): bool
    {
        return isset($t['piece layers']);
    }
}

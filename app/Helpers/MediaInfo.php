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

/**
 * @phpstan-type General array{
 *     'file_name'?: string,
 *     'format'?: string,
 *     'duration'?: string,
 *     'file_size'?: string,
 *     'bit_rate'?: string
 * }
 * @phpstan-type Video array{
 *     'format'?: string,
 *     'format_version'?: string,
 *     'codec'?: string,
 *     'width'?: string,
 *     'height'?: string,
 *     'stream_size'?: string,
 *     'writing_library'?: string,
 *     'framerate_mode'?: string,
 *     'frame_rate'?: string,
 *     'aspect_ratio'?: string,
 *     'bit_rate'?: string,
 *     'bit_rate_mode'?: string,
 *     'bit_rate_nominal'?: string,
 *     'bit_pixel_frame'?: string,
 *     'bit_depth'?: string,
 *     'encoding_settings'?: string,
 *     'language'?: string,
 *     'format_profile'?: string,
 *     'color_primaries'?: string,
 *     'title'?: string,
 *     'scan_type'?: string,
 *     'transfer_characteristics'?: string,
 *     'hdr_format'?: string,
 * }
 * @phpstan-type Audio array{
 *     'codec'?: string,
 *     'format'?: string,
 *     'bit_rate'?: string,
 *     'channels'?: string,
 *     'title'?: string,
 *     'language'?: string,
 *     'format_profile'?: string,
 *     'stream_size'?: string,
 * }
 * @phpstan-type Text array{
 *     'codec'?: string,
 *     'format'?: string,
 *     'title'?: string,
 *     'language'?: string,
 *     'default'?: string,
 *     'forced'?: string,
 * }
 * @phpstan-type Menu array{}
 */
class MediaInfo
{
    private const REGEX_SECTION = "/^(?:(?:general|video|audio|text|image|menu)(?:\s\#\d+?)*)$/i";

    /**
     * @var string[]
     */
    private const REPLACE = [
        ' '        => '',
        'channels' => 'ch',
        'channel'  => 'ch',
        '1ch'      => '1.0ch',
        '7ch'      => '6.1ch',
        '6ch'      => '5.1ch',
        '2ch'      => '2.0ch',
    ];

    /**
     * @var int[]
     */
    private const FACTORS = ['b' => 0, 'kb' => 1, 'mb' => 2, 'gb' => 3, 'tb' => 4, 'pb' => 5, 'eb' => 6, 'zb' => 7, 'yb' => 8];

    /**
     * @return array{
     *     general: ?General,
     *     video: ?non-empty-list<Video>,
     *     audio: ?non-empty-list<Audio>,
     *     text: ?non-empty-list<Text>
     * }
     */
    public function parse(string $string): array
    {
        $string = trim($string);
        $string = str_replace("\xc2\xa0", ' ', $string);
        $lines = preg_split("/\r\n|\n|\r/", $string);

        $output = [];

        if (\is_array($lines)) {
            foreach ($lines as $line) {
                $line = trim($line); // removed strtolower, unnecessary with the i-switch in the regexp (caseless) and adds problems with values; added it in the required places instead.

                if (preg_match(self::REGEX_SECTION, $line)) {
                    $section = $line;
                    $output[$section] = [];
                }

                if (isset($section)) {
                    $output[$section][] = $line;
                }
            }
        }

        if ($output !== []) {
            $output = $this->parseSections($output);
        }

        return $this->formatOutput($output);
    }

    /**
     * @param array<string, list<string>> $sections
     * @return array{
     *     'general'?: General,
     *     'video'?: list<Video>,
     *     'audio'?: list<Audio>,
     *     'text'?: list<Text>,
     * }
     */
    private function parseSections(array $sections): array
    {
        $output = [];

        foreach ($sections as $key => $section) {
            $keySection = strtolower(explode(' ', $key, 2)[0]);

            if (!empty($section)) {
                if ($keySection === 'general') {
                    $output[$keySection] = $this->parseProperty($section, $keySection);
                } elseif (\in_array($keySection, ['video', 'audio', 'text'], true)) {
                    $output[$keySection][] = $this->parseProperty($section, $keySection);
                }
            }
        }

        return $output;
    }

    /**
     * @param list<string>                     $sections
     * @param 'general'|'video'|'audio'|'text' $section
     * @return (
     *      $section is 'general' ? General
     *   : ($section is 'video'   ? Video
     *   : ($section is 'audio'   ? Audio
     *   : Text
     * )))
     */
    private function parseProperty(array $sections, string $section): array
    {
        $output = [];

        foreach ($sections as $info) {
            $property = null;
            $value = null;
            $info = explode(':', (string) $info, 2);

            if (\count($info) >= 2) {
                $property = strtolower(trim($info[0]));
                $value = trim($info[1]);
            }

            if ($property && $value) {
                switch ($section) {
                    case 'general':
                        switch ($property) {
                            case 'complete name':
                            case 'completename':
                                $output['file_name'] = self::stripPath($value);

                                break;
                            case 'format':
                                $output['format'] = $value;

                                break;
                            case 'duration':
                                $output['duration'] = $value;

                                break;
                            case 'file size':
                            case 'filesize':
                                $output['file_size'] = $this->parseFileSize($value);

                                break;
                            case 'overall bit rate':
                            case 'overallbitrate':
                                $output['bit_rate'] = $this->parseBitRate($value);

                                break;
                        }

                        break;
                    case 'video':
                        switch ($property) {
                            case 'format':
                                $output['format'] = $value;

                                break;
                            case 'format version':
                            case 'format_version':
                                $output['format_version'] = $value;

                                break;
                            case 'codec id':
                            case 'codecid':
                                $output['codec'] = $value;

                                break;
                            case 'width':
                                $output['width'] = $this->parseWidthHeight($value);

                                break;
                            case 'height':
                                $output['height'] = $this->parseWidthHeight($value);

                                break;
                            case 'stream size':
                            case 'stream_size':
                                $output['stream_size'] = $this->parseFileSize($value);

                                break;
                            case 'writing library':
                            case 'encoded_library':
                                $output['writing_library'] = $value;

                                break;
                            case 'frame rate mode':
                            case 'framerate_mode':
                                $output['framerate_mode'] = $value;

                                break;
                            case 'frame rate':
                            case 'framerate':
                                // if variable this becomes Original frame rate
                                $output['frame_rate'] = $value;

                                break;
                            case 'display aspect ratio':
                            case 'displayaspectratio':
                                $output['aspect_ratio'] = str_replace('/', ':', $value); // mediainfo sometimes uses / instead of :

                                break;
                            case 'bit rate':
                            case 'bitrate':
                                $output['bit_rate'] = $this->parseBitRate($value);

                                break;
                            case 'bit rate mode':
                            case 'bitrate_mode':
                                $output['bit_rate_mode'] = $value;

                                break;
                            case 'nominal bit rate':
                            case 'bitrate_nominal':
                                $output['bit_rate_nominal'] = $this->parseBitRate($value);

                                break;
                            case 'bits/(pixel*frame)':
                            case 'bits-(pixel*frame)':
                                $output['bit_pixel_frame'] = $value;

                                break;
                            case 'bit depth':
                            case 'bitdepth':
                                $output['bit_depth'] = $value;

                                break;
                            case 'encoding settings':
                                $output['encoding_settings'] = $value;

                                break;
                            case 'language':
                                $output['language'] = $value;

                                break;
                            case 'format profile':
                                $output['format_profile'] = $value;

                                break;
                            case 'color primaries':
                                $output['color_primaries'] = $value;

                                break;
                            case 'title':
                                $output['title'] = $value;

                                break;
                            case 'scan type':
                                $output['scan_type'] = $value;

                                break;
                            case 'transfer characteristics':
                                $output['transfer_characteristics'] = $value;

                                break;
                            case 'hdr format':
                                $output['hdr_format'] = $value;

                                break;
                        }

                        break;
                    case 'audio':
                        switch ($property) {
                            case 'codec id':
                            case 'codecid':
                                $output['codec'] = $value;

                                break;
                            case 'format':
                                $output['format'] = $value;

                                break;
                            case 'bit rate':
                            case 'bitrate':
                                $output['bit_rate'] = $this->parseBitRate($value);

                                break;
                            case 'channel(s)':
                                $output['channels'] = $this->parseAudioChannels($value);

                                break;
                            case 'title':
                                $output['title'] = $value;

                                break;
                            case 'language':
                                $output['language'] = $value;

                                break;
                            case 'format profile':
                            case 'format_profile':
                                $output['format_profile'] = $value;

                                break;
                            case 'stream size':
                            case 'stream_size':
                                $output['stream_size'] = $this->parseFileSize($value);

                                break;
                        }

                        break;
                    case 'text':
                        switch ($property) {
                            case 'codec id':
                            case 'codecid':
                                $output['codec'] = $value;

                                break;
                            case 'format':
                                $output['format'] = $value;

                                break;
                            case 'title':
                                $output['title'] = $value;

                                break;
                            case 'language':
                                $output['language'] = $value;

                                break;
                            case 'default':
                                $output['default'] = $value;

                                break;
                            case 'forced':
                                $output['forced'] = $value;

                                break;
                        }

                        break;
                }
            }
        }

        return $output;
    }

    public static function stripPath(string $string): string
    {
        $string = str_replace('\\', '/', $string);

        return pathinfo($string, PATHINFO_BASENAME);
    }

    private function parseFileSize(string $string): float
    {
        $number = (float) str_replace(' ', '', $string);
        preg_match('/[KMGTPEZ]/i', $string, $size);

        if (!empty($size[0])) {
            $number = $this->computerSize($number, $size[0].'b');
        }

        return $number;
    }

    private function parseBitRate(string $string): string
    {
        return str_replace([' ', 'kbps'], ['', ' kbps'], strtolower($string));
    }

    private function parseWidthHeight(string $string): string
    {
        return str_replace(['pixels', ' '], ' ', strtolower($string));
    }

    private function parseAudioChannels(string $string): string
    {
        return str_ireplace(array_keys(self::REPLACE), self::REPLACE, $string);
    }

    /**
     * @param array{
     *      'general'?: General,
     *      'video'?: list<Video>,
     *      'audio'?: list<Audio>,
     *      'text'?: list<Text>,
     * } $data
     * @return array{
     *     general: ?General,
     *     video: ?non-empty-list<Video>,
     *     audio: ?non-empty-list<Audio>,
     *     text: ?non-empty-list<Text>,
     * }
     */
    private function formatOutput(array $data): array
    {
        $output = [];
        $output['general'] = empty($data['general']) ? null : $data['general'];
        $output['video'] = empty($data['video']) ? null : $data['video'];
        $output['audio'] = empty($data['audio']) ? null : $data['audio'];
        $output['text'] = empty($data['text']) ? null : $data['text'];

        return $output;
    }

    private function computerSize(float $number, string $size): float
    {
        $bytes = $number;
        $size = strtolower($size);

        if (isset(self::FACTORS[$size])) {
            return (float) number_format($bytes * (1_024 ** self::FACTORS[$size]), 2, '.', '');
        }

        return $bytes;
    }
}

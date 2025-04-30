<?php

declare(strict_types=1);

/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 *
 * @author     CvT <convert.banister491@passinbox.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Helpers;

use App\Models\WhitelistedImageUrl;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Exception;
use Log;

class ImageHelper
{
    /**
     * Process an image (file or URL) for torrent cover or banner.
     *
     * @param  UploadedFile|string                        $source    Image file or URL
     * @param  string                                     $torrentId Torrent ID for naming
     * @param  string                                     $type      'cover' or 'banner'
     * @return array{path: string|null, url: string}|null
     */
    public static function processTorrentImage($source, string $torrentId, string $type): ?array
    {
        $handling = config('image.remote_image_handling');
        $disk = $type === 'cover' ? 'torrent-covers' : 'torrent-banners';
        $filename = "torrent-{$type}_{$torrentId}.webp";

        // Handle Banner
        if ($type === 'banner') {
            return self::processBanner($source, $torrentId, $disk, $filename, $type);
        }

        // Handle Cover
        if ($source instanceof UploadedFile) {
            return self::processUploadedCover($source, $disk, $filename, $torrentId, $type);
        }

        if (!self::isValidImageUrl($source)) {
            Log::error("Invalid image URL in processTorrentImage", [
                'url'        => $source,
                'torrent_id' => $torrentId,
                'type'       => $type,
            ]);

            return null;
        }

        if ($handling === 'use_url') {
            return ['path' => null, 'url' => $source];
        }

        if ($handling === 'whitelist_or_save' && self::isWhitelistedUrl($source)) {
            return ['path' => null, 'url' => $source];
        }

        // Save to server
        $result = self::processRemoteCover($source, $disk, $filename, $torrentId, $type);

        if (!$result) {
            Log::error("Failed to process remote cover in processTorrentImage", [
                'url'        => $source,
                'torrent_id' => $torrentId,
                'type'       => $type,
            ]);
        }

        return $result;
    }

    /**
     * Validate if a URL points to a valid image.
     */
    private static function isValidImageUrl(string $url): bool
    {
        try {
            $response = Http::timeout(5)->get($url);

            if ($response->successful() && str_starts_with($response->header('Content-Type'), 'image/')) {
                return true;
            }
            Log::warning("Invalid image URL response", [
                'url'          => $url,
                'status'       => $response->status(),
                'content_type' => $response->header('Content-Type'),
            ]);

            return false;
        } catch (Exception $e) {
            Log::error("Exception in isValidImageUrl", [
                'url'   => $url,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Check if a URL matches any whitelisted patterns from WhitelistedImageUrl model.
     */
    private static function isWhitelistedUrl(string $url): bool
    {
        $patterns = cache()->remember('whitelisted-image-urls', now()->addHour(), fn () => WhitelistedImageUrl::pluck('pattern')->toArray());

        foreach ($patterns as $wildcard) {
            $regex = self::wildcardToRegex($wildcard);

            if (@preg_match($regex, $url)) {
                return true;
            }
        }

        return false;
    }

    private static function wildcardToRegex(string $wildcard): string
    {
        $escaped = preg_quote($wildcard, '/');
        $regex = str_replace(['\*\*', '\*'], ['.*', '[^\/\.]*'], $escaped);

        return '/^'.$regex.'$/i';
    }

    /**
     * Process uploaded cover image.
     *
     * @param  UploadedFile                     $file
     * @param  string                           $disk
     * @param  string                           $filename
     * @param  string                           $torrentId
     * @param  string                           $type
     * @return array{path: string, url: string}
     */
    private static function processUploadedCover(UploadedFile $file, string $disk, string $filename, string $torrentId, string $type): array
    {
        $path = Storage::disk($disk)->path($filename);
        $image = Image::make($file->getRealPath());
        $image->resize(500, 500, function ($constraint): void {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $image->encode('webp', 90)->save($path);
        self::deleteUnusedTorrentImage($torrentId, $type, legacyOnly: true);

        return ['path' => $filename, 'url' => url("authenticated-images/{$disk}/{$torrentId}.webp")];
    }

    /**
     * Process remote cover image.
     *
     * @param  string                                $url
     * @param  string                                $disk
     * @param  string                                $filename
     * @param  string                                $torrentId
     * @param  string                                $type
     * @return array{path: string, url: string}|null
     */
    private static function processRemoteCover(string $url, string $disk, string $filename, string $torrentId, string $type): ?array
    {
        try {
            $response = Http::timeout(5)->get($url);

            if (!$response->successful()) {
                Log::error("HTTP request failed in processRemoteCover", [
                    'url'     => $url,
                    'status'  => $response->status(),
                    'headers' => $response->headers(),
                ]);

                return null;
            }

            $path = Storage::disk($disk)->path($filename);
            $image = Image::make($response->body());
            $image->resize(500, 500, function ($constraint): void {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $image->encode('webp', 90)->save($path);
            self::deleteUnusedTorrentImage($torrentId, $type, legacyOnly: true);

            return [
                'path' => $filename,
                'url'  => url("authenticated-images/{$disk}/{$torrentId}.webp"),
            ];
        } catch (Exception $e) {
            Log::error("Exception in processRemoteCover", [
                'url'      => $url,
                'disk'     => $disk,
                'filename' => $filename,
                'error'    => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Process banner image.
     *
     * @param  UploadedFile|string                        $source
     * @param  string                                     $torrentId
     * @param  string                                     $disk
     * @param  string                                     $filename
     * @param  string                                     $type
     * @return array{path: string|null, url: string}|null
     */
    private static function processBanner($source, string $torrentId, string $disk, string $filename, string $type): ?array
    {
        if ($source instanceof UploadedFile) {
            $path = Storage::disk($disk)->path($filename);
            Image::make($source->getRealPath())->fit(960, 540)->encode('webp', 90)->save($path);
            self::deleteUnusedTorrentImage($torrentId, $type, legacyOnly: true);

            return [
                'path' => $filename,
                'url'  => url("authenticated-images/{$disk}/{$torrentId}.webp"),
            ];
        }

        if (self::isValidImageUrl($source)) {
            $handling = config('image.remote_image_handling');

            if ($handling === 'use_url') {
                return ['path' => null, 'url' => $source];
            }

            if ($handling === 'whitelist_or_save' && self::isWhitelistedUrl($source)) {
                return ['path' => null, 'url' => $source];
            }

            try {
                $response = Http::timeout(5)->get($source);

                if ($response->successful()) {
                    $path = Storage::disk($disk)->path($filename);
                    $image = Image::make($response->body());
                    $image->resize(500, 500, function ($constraint): void {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $image->encode('webp', 90)->save($path);
                    self::deleteUnusedTorrentImage($torrentId, $type, legacyOnly: true);

                    return [
                        'path' => $filename,
                        'url'  => url("authenticated-images/{$disk}/{$torrentId}.webp"),
                    ];
                }
            } catch (Exception $e) {
                Log::error("Exception in processBanner", [
                    'url'        => $source,
                    'torrent_id' => $torrentId,
                    'disk'       => $disk,
                    'filename'   => $filename,
                    'error'      => $e->getMessage(),
                ]);

                return null;
            }
        }

        return null;
    }

    /**
     * Delete Torrent Image.
     */
    public static function deleteUnusedTorrentImage(string $torrentId, string $type, bool $legacyOnly = false): void
    {
        $disk = $type === 'cover' ? 'torrent-covers' : 'torrent-banners';

        $candidates = $legacyOnly
            ? ["torrent-{$type}_{$torrentId}.jpg"]
            : [
                "torrent-{$type}_{$torrentId}.webp",
                "torrent-{$type}_{$torrentId}.jpg",
            ];

        foreach ($candidates as $filename) {
            if (Storage::disk($disk)->exists($filename)) {
                Storage::disk($disk)->delete($filename);

                break;
            }
        }
    }
}

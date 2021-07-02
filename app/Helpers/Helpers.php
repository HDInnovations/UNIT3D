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
if (! function_exists('appurl')) {
    function appurl()
    {
        return config('app.url');
    }
}

if (! function_exists('hrefProfile')) {
    function href_profile($user)
    {
        $appurl = appurl();

        return sprintf('%s/users/%s', $appurl, $user->username);
    }
}

if (! function_exists('hrefArticle')) {
    function href_article($article)
    {
        $appurl = appurl();

        return sprintf('%s/articles/%s', $appurl, $article->id);
    }
}

if (! function_exists('hrefTorrent')) {
    function href_torrent($torrent)
    {
        $appurl = appurl();

        return sprintf('%s/torrents/%s', $appurl, $torrent->id);
    }
}

if (! function_exists('hrefRequest')) {
    function href_request($torrentRequest)
    {
        $appurl = appurl();

        return sprintf('%s/requests/%s', $appurl, $torrentRequest->id);
    }
}

if (! function_exists('hrefPoll')) {
    function href_poll($poll)
    {
        $appurl = appurl();

        return sprintf('%s/polls/%s', $appurl, $poll->id);
    }
}

if (! function_exists('hrefPlaylist')) {
    function href_playlist($playlist)
    {
        $appurl = appurl();

        return sprintf('%s/playlists/%s', $appurl, $playlist->id);
    }
}

if (! function_exists('hrefCollection')) {
    function href_collection($collection)
    {
        $appurl = appurl();

        return sprintf('%s/mediahub/collections/%s', $appurl, $collection->id);
    }
}

if (! function_exists('tmdbImage')) {
    function tmdb_image($type, $original)
    {
        $new = match ($type) {
            'back_big'     => 'w1280',
            'back_small'   => 'w780',
            'poster_big'   => 'w500',
            'poster_mid'   => 'w342',
            'cast_face'    => 'w138_and_h175_face',
            'cast_mid'     => 'w185',
            'cast_big'     => 'w300',
            'still_mid'    => 'w400',
            'logo_small'   => 'h60',
            'logo_mid'     => 'w300',
            default        => 'original',
        };

        return \str_replace('/original/', '/'.$new.'/', $original);
    }
}

if (! function_exists('modalStyle')) {
    function modal_style()
    {
        return (auth()->user()->style == 0) ? '' : ' modal-dark';
    }
}

if (! function_exists('ratingColor')) {
    function rating_color($number)
    {
        if ($number > 0 && $number <= 3.9) {
            return 'text-danger';
        }

        if ($number >= 4 && $number <= 6.9) {
            return 'text-warning';
        }

        if ($number >= 7 && $number <= 10) {
            return 'text-success';
        }
    }
}

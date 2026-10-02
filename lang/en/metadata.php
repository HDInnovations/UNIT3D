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
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

return [
    'errors' => [
        'identifier-required'      => 'An identifier is required to fetch metadata.',
        'category-not-lookupable'  => 'This category does not support metadata lookup.',
        'invalid-numeric-id'       => 'Enter a valid numeric ID.',
        'invalid-isbn'             => 'Enter a valid ISBN-10 or ISBN-13, or an Open Library edition ID (e.g. OL7353617M).',
        'invalid-musicbrainz-id'   => 'Enter a valid MusicBrainz release UUID.',
        'not-found'                => 'No metadata was found for this identifier.',
        'provider-unavailable'     => 'The metadata provider is currently unavailable. Please try again later.',
        'tmdb-not-configured'      => 'TMDB metadata lookup is not configured on this server.',
        'igdb-not-configured'      => 'IGDB metadata lookup is not configured on this server.',
        'selection-expired' => 'This metadata selection has expired or does not belong to this form. Fetch metadata again.',
        'selection-mismatch' => 'The selected metadata does not match the supplied identifier. Fetch metadata again.',
        'work-mismatch' => 'The selected title does not match this torrent category or identifier.',
        'draft-unavailable' => 'This upload draft is not available to your account.',
    ],

    'warnings' => [
        'tmdb-english-fallback'      => 'No Czech description is available from TMDB for this title; showing the English description instead.',
        'book-no-czech-description' => 'No verified Czech description is available for this book; showing the description in its original language, if any.',
        'book-no-description'       => 'This Czech edition has no description available.',
    ],

    'sections' => [
        'summary'       => 'Metadata summary',
        'form'          => 'Content metadata',
        'form_hint'     => 'Read-only fields. Enter an identifier and click Fetch metadata; available details will fill these fields.',
        'not_loaded'    => 'Filled after fetching metadata',
        'unavailable'   => 'Not provided by the source',
        'advanced'      => 'Full provider data',
        'show_advanced' => 'Show full provider data',
        'hide_advanced' => 'Hide full provider data',
    ],

    'fields' => [
        'yes'                   => 'Yes',
        'no'                    => 'No',
        'minutes'               => ':count min',
        'disc'                  => 'Disc :number',

        // Shared
        'year'                  => 'Year',
        'release_date'          => 'Release date',
        'genres'                => 'Genres',
        'languages'             => 'Languages',
        'language'              => 'Language',
        'rating'                => 'Rating',
        'rating_count'          => 'Rating votes',
        'status'                => 'Status',
        'country'               => 'Country',
        'publisher'             => 'Publisher',
        'published_date'        => 'Published date',
        'format'                => 'Format',
        'formats'               => 'Formats',
        'dimensions'            => 'Dimensions',
        'subjects'              => 'Subjects',
        'page_count'            => 'Pages',
        'isbn_10'                => 'ISBN-10',
        'isbn_13'                => 'ISBN-13',
        'classifications'       => 'Classifications',

        // IGDB (game)
        'developers'            => 'Developers',
        'publishers'            => 'Publishers',
        'porting_companies'     => 'Porting studios',
        'supporting_companies'  => 'Supporting studios',
        'game_engines'          => 'Game engines',
        'platforms'             => 'Platforms',
        'themes'                => 'Themes',
        'game_modes'            => 'Game modes',
        'player_perspectives'   => 'Player perspectives',
        'storyline'             => 'Storyline',
        'franchise'             => 'Franchise',
        'collection'            => 'Collection',
        'similar_games'         => 'Related games',
        'age_ratings'           => 'Age ratings',
        'aggregated_rating'     => 'Critic rating',
        'aggregated_rating_count' => 'Critic rating votes',
        'multiplayer'                     => 'Multiplayer',
        'multiplayer_campaign_coop'       => 'Campaign co-op',
        'multiplayer_drop_in'             => 'Drop-in',
        'multiplayer_lan_coop'            => 'LAN co-op',
        'multiplayer_offline_coop'        => 'Offline co-op',
        'multiplayer_online_coop'         => 'Online co-op',
        'multiplayer_split_screen'        => 'Split screen',
        'multiplayer_online_split_screen' => 'Online split screen',
        'multiplayer_offline_max'         => 'Offline max players',
        'multiplayer_online_max'          => 'Online max players',
        'websites'              => 'Websites',

        // TMDB (movie/tv)
        'original_title'        => 'Original title',
        'original_language'     => 'Original language',
        'tagline'                => 'Tagline',
        'runtime'                => 'Runtime',
        'budget'                 => 'Budget',
        'revenue'                => 'Revenue',
        'companies'              => 'Production companies',
        'countries'              => 'Production countries',
        'spoken_languages'       => 'Spoken languages',
        'networks'               => 'Networks',
        'seasons'                => 'Seasons',
        'episodes'               => 'Episodes',
        'creators'               => 'Creators',
        'directors'              => 'Directors',
        'cast'                   => 'Cast',
        'adult'                  => 'Adult content',
        'homepage'               => 'Homepage',
        'imdb_id'                => 'IMDb ID',
        'tvdb_id'                => 'TVDB ID',
        'keywords'               => 'Keywords',
        'alternative_titles'     => 'Alternative titles',
        'recommendations'        => 'Recommendations',
        'videos'                 => 'Videos',
        'certification'          => 'Certification',

        // MusicBrainz (music)
        'artists'                => 'Artists',
        'labels'                 => 'Labels',
        'catalog_numbers'        => 'Catalog numbers',
        'barcode'                => 'Barcode',
        'packaging'              => 'Packaging',
        'tags'                   => 'Tags',
        'tracklist'              => 'Tracklist',

        // Books
        'authors'                => 'Authors',
    ],
];

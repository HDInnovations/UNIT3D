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

namespace App\Services\Metadata;

/**
 * Turns a raw provider payload (IGDB, TMDB, MusicBrainz, Open Library or
 * Google Books) into a flat, localized, human-readable list of label/value
 * rows for display. Purely a presentation helper: it never mutates or
 * persists anything, and it never fabricates data — fields absent from the
 * raw payload are simply omitted from the result.
 *
 * @phpstan-type DetailRow array{key: string, label: string, value: string}
 */
final class MetadataDetails
{
    /**
     * @param array<string, mixed> $raw
     *
     * @return list<DetailRow>
     */
    public static function forSource(string $source, array $raw): array
    {
        return match (self::normalizeSource($source)) {
            'igdb'          => self::igdb($raw),
            'tmdb'          => self::tmdb($raw),
            'musicbrainz'   => self::musicBrainz($raw),
            'open-library'  => self::openLibrary($raw),
            'google-books'  => self::googleBooks($raw),
            default         => [],
        };
    }

    private static function normalizeSource(string $source): string
    {
        return strtolower(str_replace(' ', '-', trim($source)));
    }

    // ------------------------------------------------------------------
    // IGDB (game)
    // ------------------------------------------------------------------

    /**
     * @param array<string, mixed> $game
     *
     * @return list<DetailRow>
     */
    private static function igdb(array $game): array
    {
        $rows = [];

        $firstReleaseDate = $game['first_release_date'] ?? null;
        if (\is_int($firstReleaseDate) || $firstReleaseDate instanceof \DateTimeInterface) {
            $rows[] = self::dateRow('release_date', $firstReleaseDate);
        } else {
            foreach (self::asList($game['release_dates'] ?? null) as $releaseDate) {
                if (\is_array($releaseDate) && (\is_int($releaseDate['date'] ?? null) || ($releaseDate['date'] ?? null) instanceof \DateTimeInterface)) {
                    $rows[] = self::dateRow('release_date', $releaseDate['date']);
                    break;
                }

                if (\is_array($releaseDate) && \is_string($releaseDate['human'] ?? null) && $releaseDate['human'] !== '') {
                    $rows[] = self::row('release_date', $releaseDate['human']);
                    break;
                }
            }
        }

        $developers = [];
        $publishers = [];
        $porting = [];
        $supporting = [];

        foreach (self::asList($game['involved_companies'] ?? null) as $involved) {
            $name = self::companyName($involved);

            if ($name === null) {
                continue;
            }

            if (($involved['developer'] ?? false) === true) {
                $developers[] = $name;
            }

            if (($involved['publisher'] ?? false) === true) {
                $publishers[] = $name;
            }

            if (($involved['porting'] ?? false) === true) {
                $porting[] = $name;
            }

            if (($involved['supporting'] ?? false) === true) {
                $supporting[] = $name;
            }
        }

        $rows = self::pushListRow($rows, 'developers', $developers);
        $rows = self::pushListRow($rows, 'publishers', $publishers);
        $rows = self::pushListRow($rows, 'porting_companies', $porting);
        $rows = self::pushListRow($rows, 'supporting_companies', $supporting);

        $rows = self::pushListRow($rows, 'genres', self::pluckNames($game['genres'] ?? null));
        $rows = self::pushListRow($rows, 'game_engines', self::pluckNames($game['game_engines'] ?? null));
        $rows = self::pushListRow($rows, 'platforms', self::pluckNames($game['platforms'] ?? null));
        $rows = self::pushListRow($rows, 'themes', self::pluckNames($game['themes'] ?? null));
        $rows = self::pushListRow($rows, 'game_modes', self::pluckNames($game['game_modes'] ?? null));
        $rows = self::pushListRow($rows, 'player_perspectives', self::pluckNames($game['player_perspectives'] ?? null));

        if (\is_string($game['storyline'] ?? null) && $game['storyline'] !== '') {
            $rows[] = self::row('storyline', $game['storyline']);
        }

        $franchises = self::pluckNames($game['franchises'] ?? null);
        if ($franchises === [] && \is_array($game['franchise'] ?? null)) {
            $franchises = self::pluckNames([$game['franchise']]);
        }
        $rows = self::pushListRow($rows, 'franchise', $franchises);

        $collections = self::pluckNames($game['collections'] ?? null);
        if ($collections === [] && \is_array($game['collection'] ?? null)) {
            $collections = self::pluckNames([$game['collection']]);
        }
        $rows = self::pushListRow($rows, 'collection', $collections);

        $rows = self::pushListRow($rows, 'similar_games', self::pluckNames($game['similar_games'] ?? null));

        $ageRatings = [];
        foreach (self::asList($game['age_ratings'] ?? null) as $ageRating) {
            if (!\is_array($ageRating)) {
                continue;
            }

            $organization = \is_array($ageRating['organization'] ?? null) ? ($ageRating['organization']['name'] ?? null) : null;

            // IGDB v4 exposes the human-readable rating as `rating_category.rating`
            // (a relation) when it is included in the query. The bare
            // `rating_category`/legacy `rating` field is an enum id with no
            // meaning outside IGDB, so it is intentionally never shown.
            $ratingValue = \is_array($ageRating['rating_category'] ?? null) && \is_string($ageRating['rating_category']['rating'] ?? null) && $ageRating['rating_category']['rating'] !== ''
                ? $ageRating['rating_category']['rating']
                : (\is_string($ageRating['rating'] ?? null) && $ageRating['rating'] !== '' ? $ageRating['rating'] : null);

            if ($ratingValue === null) {
                continue;
            }

            $ageRatings[] = \is_string($organization) && $organization !== '' ? "{$organization} {$ratingValue}" : $ratingValue;
        }
        $rows = self::pushListRow($rows, 'age_ratings', $ageRatings);

        $languages = [];
        foreach (self::asList($game['language_supports'] ?? null) as $support) {
            $language = \is_array($support['language'] ?? null) ? ($support['language']['name'] ?? null) : null;

            if (\is_string($language) && $language !== '') {
                $languages[] = $language;
            }
        }
        $rows = self::pushListRow($rows, 'languages', array_values(array_unique($languages)));

        if (isset($game['rating'])) {
            $rows[] = self::row('rating', self::numberToString($game['rating']));
        }

        if (isset($game['rating_count'])) {
            $rows[] = self::row('rating_count', self::numberToString($game['rating_count']));
        }

        if (isset($game['aggregated_rating'])) {
            $rows[] = self::row('aggregated_rating', self::numberToString($game['aggregated_rating']));
        }

        if (isset($game['aggregated_rating_count'])) {
            $rows[] = self::row('aggregated_rating_count', self::numberToString($game['aggregated_rating_count']));
        }

        $multiplayerFeatures = [];
        $multiplayerFlagFields = [
            'campaigncoop'      => 'multiplayer_campaign_coop',
            'dropin'            => 'multiplayer_drop_in',
            'lancoop'           => 'multiplayer_lan_coop',
            'offlinecoop'       => 'multiplayer_offline_coop',
            'onlinecoop'        => 'multiplayer_online_coop',
            'splitscreen'       => 'multiplayer_split_screen',
            'splitscreenonline' => 'multiplayer_online_split_screen',
        ];

        foreach (self::asList($game['multiplayer_modes'] ?? null) as $mode) {
            if (!\is_array($mode)) {
                continue;
            }

            foreach ($multiplayerFlagFields as $flag => $labelField) {
                if (($mode[$flag] ?? false) === true) {
                    $multiplayerFeatures[$labelField] = __("metadata.fields.{$labelField}");
                }
            }

            foreach (['offlinemax' => 'multiplayer_offline_max', 'onlinemax' => 'multiplayer_online_max'] as $countField => $labelField) {
                if (\is_int($mode[$countField] ?? null) && $mode[$countField] > 0) {
                    $multiplayerFeatures[$labelField] = __("metadata.fields.{$labelField}").' '.$mode[$countField];
                }
            }
        }
        $rows = self::pushListRow($rows, 'multiplayer', array_values($multiplayerFeatures));

        $websites = [];
        foreach (self::asList($game['websites'] ?? null) as $website) {
            if (\is_string($website['url'] ?? null) && $website['url'] !== '') {
                $websites[] = $website['url'];
            }
        }
        $rows = self::pushListRow($rows, 'websites', $websites);

        return $rows;
    }

    // ------------------------------------------------------------------
    // TMDB (movie/tv)
    // ------------------------------------------------------------------

    /**
     * @param array<string, mixed> $data
     *
     * @return list<DetailRow>
     */
    private static function tmdb(array $data): array
    {
        $isTv = isset($data['first_air_date']) || isset($data['number_of_seasons']) || isset($data['name']) && !isset($data['title']);
        $rows = [];

        $releaseDate = $isTv ? ($data['first_air_date'] ?? null) : ($data['release_date'] ?? null);
        if (\is_string($releaseDate) && $releaseDate !== '') {
            $rows[] = self::row('release_date', $releaseDate);
        }

        $originalTitle = $isTv ? ($data['original_name'] ?? null) : ($data['original_title'] ?? null);
        if (\is_string($originalTitle) && $originalTitle !== '') {
            $rows[] = self::row('original_title', $originalTitle);
        }

        if (\is_string($data['original_language'] ?? null) && $data['original_language'] !== '') {
            $rows[] = self::row('original_language', $data['original_language']);
        }

        if (\is_string($data['status'] ?? null) && $data['status'] !== '') {
            $rows[] = self::row('status', $data['status']);
        }

        if (\is_string($data['tagline'] ?? null) && $data['tagline'] !== '') {
            $rows[] = self::row('tagline', $data['tagline']);
        }

        if (!$isTv && isset($data['runtime']) && \is_numeric($data['runtime']) && (int) $data['runtime'] > 0) {
            $rows[] = self::row('runtime', __('metadata.fields.minutes', ['count' => (int) $data['runtime']]));
        }

        if ($isTv) {
            $episodeRunTimes = self::asList($data['episode_run_time'] ?? null);
            $episodeRunTimes = array_values(array_filter($episodeRunTimes, static fn (mixed $value): bool => \is_int($value) || \is_float($value)));
            if ($episodeRunTimes !== []) {
                $rows[] = self::row('runtime', __('metadata.fields.minutes', ['count' => (int) $episodeRunTimes[0]]));
            }
        }

        if (!$isTv && isset($data['budget']) && \is_numeric($data['budget']) && (int) $data['budget'] > 0) {
            $rows[] = self::row('budget', self::numberToString($data['budget']));
        }

        if (!$isTv && isset($data['revenue']) && \is_numeric($data['revenue']) && (int) $data['revenue'] > 0) {
            $rows[] = self::row('revenue', self::numberToString($data['revenue']));
        }

        $rows = self::pushListRow($rows, 'genres', self::pluckNames($data['genres'] ?? null));
        $rows = self::pushListRow($rows, 'companies', self::pluckNames($data['production_companies'] ?? null));
        $rows = self::pushListRow($rows, 'countries', self::pluckNames($data['production_countries'] ?? null));

        $spokenLanguages = [];
        foreach (self::asList($data['spoken_languages'] ?? null) as $language) {
            $name = $language['english_name'] ?? $language['name'] ?? null;
            if (\is_string($name) && $name !== '') {
                $spokenLanguages[] = $name;
            }
        }
        $rows = self::pushListRow($rows, 'spoken_languages', $spokenLanguages);

        if ($isTv) {
            $rows = self::pushListRow($rows, 'networks', self::pluckNames($data['networks'] ?? null));

            if (isset($data['number_of_seasons']) && \is_numeric($data['number_of_seasons'])) {
                $rows[] = self::row('seasons', self::numberToString($data['number_of_seasons']));
            }

            if (isset($data['number_of_episodes']) && \is_numeric($data['number_of_episodes'])) {
                $rows[] = self::row('episodes', self::numberToString($data['number_of_episodes']));
            }

            $creators = [];
            foreach (self::asList($data['created_by'] ?? null) as $creator) {
                if (\is_string($creator['name'] ?? null) && $creator['name'] !== '') {
                    $creators[] = $creator['name'];
                }
            }
            $rows = self::pushListRow($rows, 'creators', $creators);
        }

        $credits = \is_array($data['credits'] ?? null)
            ? $data['credits']
            : (\is_array($data['aggregate_credits'] ?? null) ? $data['aggregate_credits'] : null);

        if ($credits !== null) {
            $directors = [];
            foreach (self::asList($credits['crew'] ?? null) as $crew) {
                if (($crew['job'] ?? null) === 'Director' && \is_string($crew['name'] ?? null) && $crew['name'] !== '') {
                    $directors[] = $crew['name'];
                }

                foreach (self::asList(\is_array($crew) ? ($crew['jobs'] ?? null) : null) as $job) {
                    if (\is_array($job) && ($job['job'] ?? null) === 'Director' && \is_string($crew['name'] ?? null) && $crew['name'] !== '') {
                        $directors[] = $crew['name'];
                    }
                }
            }
            $rows = self::pushListRow($rows, 'directors', array_values(array_unique($directors)));

            $cast = [];
            foreach (self::asList($credits['cast'] ?? null) as $castMember) {
                if (\is_string($castMember['name'] ?? null) && $castMember['name'] !== '') {
                    $cast[] = $castMember['name'];
                }
            }
            $rows = self::pushListRow($rows, 'cast', \array_slice(array_values(array_unique($cast)), 0, 20));
        }

        $alternativeTitles = [];
        foreach (self::asList($data['alternative_titles']['titles'] ?? $data['alternative_titles']['results'] ?? null) as $alternativeTitle) {
            $title = $alternativeTitle['title'] ?? $alternativeTitle['name'] ?? null;
            if (\is_string($title) && $title !== '') {
                $alternativeTitles[] = $title;
            }
        }
        $rows = self::pushListRow($rows, 'alternative_titles', array_values(array_unique($alternativeTitles)));

        $recommendations = [];
        foreach (self::asList($data['recommendations']['results'] ?? null) as $recommendation) {
            $title = $recommendation['title'] ?? $recommendation['name'] ?? null;
            if (\is_string($title) && $title !== '') {
                $recommendations[] = $title;
            }
        }
        $rows = self::pushListRow($rows, 'recommendations', \array_slice(array_values(array_unique($recommendations)), 0, 20));

        $videos = [];
        foreach (self::asList($data['videos']['results'] ?? null) as $video) {
            $name = $video['name'] ?? $video['key'] ?? null;
            if (\is_string($name) && $name !== '') {
                $videos[] = $name;
            }
        }
        $rows = self::pushListRow($rows, 'videos', \array_slice(array_values(array_unique($videos)), 0, 20));

        if (isset($data['vote_average'])) {
            $rows[] = self::row('rating', self::numberToString($data['vote_average']));
        }

        if (isset($data['vote_count'])) {
            $rows[] = self::row('rating_count', self::numberToString($data['vote_count']));
        }

        if (\array_key_exists('adult', $data)) {
            $rows[] = self::row('adult', self::boolToString($data['adult']));
        }

        if (\is_string($data['homepage'] ?? null) && $data['homepage'] !== '') {
            $rows[] = self::row('homepage', $data['homepage']);
        }

        $imdbId = $data['imdb_id'] ?? $data['external_ids']['imdb_id'] ?? null;
        if (\is_string($imdbId) && $imdbId !== '') {
            $rows[] = self::row('imdb_id', $imdbId);
        }

        $tvdbId = $data['external_ids']['tvdb_id'] ?? null;
        if ((\is_string($tvdbId) && $tvdbId !== '') || \is_int($tvdbId)) {
            $rows[] = self::row('tvdb_id', (string) $tvdbId);
        }

        $keywords = self::pluckNames($data['keywords']['keywords'] ?? $data['keywords']['results'] ?? null);
        $rows = self::pushListRow($rows, 'keywords', $keywords);

        $certification = self::tmdbCertification($data, $isTv);
        if ($certification !== null) {
            $rows[] = self::row('certification', $certification);
        }

        return $rows;
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function tmdbCertification(array $data, bool $isTv): ?string
    {
        if ($isTv) {
            foreach (self::asList($data['content_ratings']['results'] ?? null) as $entry) {
                if (\is_string($entry['rating'] ?? null) && $entry['rating'] !== '') {
                    return $entry['rating'];
                }
            }

            return null;
        }

        foreach (self::asList($data['release_dates']['results'] ?? null) as $countryEntry) {
            foreach (self::asList(\is_array($countryEntry) ? ($countryEntry['release_dates'] ?? null) : null) as $release) {
                if (\is_string($release['certification'] ?? null) && $release['certification'] !== '') {
                    return $release['certification'];
                }
            }
        }

        return null;
    }

    // ------------------------------------------------------------------
    // MusicBrainz (music)
    // ------------------------------------------------------------------

    /**
     * @param array<string, mixed> $release
     *
     * @return list<DetailRow>
     */
    private static function musicBrainz(array $release): array
    {
        $rows = [];

        $artists = [];
        foreach (self::asList($release['artist-credit'] ?? null) as $credit) {
            if (\is_string($credit['name'] ?? null) && $credit['name'] !== '') {
                $artists[] = $credit['name'];
            }
        }
        $rows = self::pushListRow($rows, 'artists', $artists);

        $releaseDate = $release['date'] ?? $release['first-release-date'] ?? null;
        if (\is_string($releaseDate) && $releaseDate !== '') {
            $rows[] = self::row('release_date', $releaseDate);
        }

        if (\is_string($release['country'] ?? null) && $release['country'] !== '') {
            $rows[] = self::row('country', $release['country']);
        }

        $labels = [];
        $catalogNumbers = [];
        foreach (self::asList($release['label-info'] ?? null) as $labelInfo) {
            $labelName = \is_array($labelInfo['label'] ?? null) ? ($labelInfo['label']['name'] ?? null) : null;
            if (\is_string($labelName) && $labelName !== '') {
                $labels[] = $labelName;
            }

            if (\is_string($labelInfo['catalog-number'] ?? null) && $labelInfo['catalog-number'] !== '') {
                $catalogNumbers[] = $labelInfo['catalog-number'];
            }
        }
        $rows = self::pushListRow($rows, 'labels', $labels);
        $rows = self::pushListRow($rows, 'catalog_numbers', $catalogNumbers);

        if (\is_string($release['barcode'] ?? null) && $release['barcode'] !== '') {
            $rows[] = self::row('barcode', $release['barcode']);
        }

        if (\is_string($release['status'] ?? null) && $release['status'] !== '') {
            $rows[] = self::row('status', $release['status']);
        }

        if (\is_string($release['packaging'] ?? null) && $release['packaging'] !== '') {
            $rows[] = self::row('packaging', $release['packaging']);
        }

        $formats = [];
        foreach (self::asList($release['media'] ?? null) as $medium) {
            if (\is_string($medium['format'] ?? null) && $medium['format'] !== '') {
                $formats[] = $medium['format'];
            }
        }
        $rows = self::pushListRow($rows, 'formats', array_values(array_unique($formats)));

        $genres = self::pluckNames($release['genres'] ?? null);
        if ($genres === [] && \is_array($release['release-group'] ?? null)) {
            $genres = self::pluckNames($release['release-group']['genres'] ?? null);
        }
        $rows = self::pushListRow($rows, 'genres', $genres);

        $rows = self::pushListRow($rows, 'tags', self::pluckNames($release['tags'] ?? null));

        foreach (self::asList($release['media'] ?? null) as $index => $medium) {
            $tracks = [];
            foreach (self::asList($medium['tracks'] ?? null) as $track) {
                $position = $track['position'] ?? null;
                $title = $track['title'] ?? null;
                $length = $track['length'] ?? null;

                if (!\is_string($title) || $title === '') {
                    continue;
                }

                $line = (\is_int($position) || \is_string($position)) ? "{$position}. {$title}" : $title;

                if (\is_int($length)) {
                    $line .= ' ('.self::millisecondsToDuration($length).')';
                }

                $tracks[] = $line;
            }

            if ($tracks === []) {
                continue;
            }

            $discNumber = $medium['position'] ?? ($index + 1);
            $rows[] = self::row('tracklist', __('metadata.fields.disc', ['number' => $discNumber]).': '.implode("\n", $tracks));
        }

        return $rows;
    }

    // ------------------------------------------------------------------
    // Open Library (book)
    // ------------------------------------------------------------------

    /**
     * @param array<string, mixed> $book
     *
     * @return list<DetailRow>
     */
    private static function openLibrary(array $book): array
    {
        $rows = [];

        $authors = self::asList($book['author_names'] ?? null);
        $authors = array_values(array_filter($authors, static fn (mixed $name): bool => \is_string($name) && $name !== ''));
        $rows = self::pushListRow($rows, 'authors', $authors);

        $publishers = self::asList($book['publishers'] ?? null);
        $publishers = array_values(array_filter($publishers, static fn (mixed $name): bool => \is_string($name) && $name !== ''));
        $rows = self::pushListRow($rows, 'publisher', $publishers);

        if (\is_string($book['publish_date'] ?? null) && $book['publish_date'] !== '') {
            $rows[] = self::row('published_date', $book['publish_date']);
        }

        $isbn10 = self::asList($book['isbn_10'] ?? null);
        $isbn10 = array_values(array_filter($isbn10, static fn (mixed $v): bool => \is_string($v) && $v !== ''));
        $rows = self::pushListRow($rows, 'isbn_10', $isbn10);

        $isbn13 = self::asList($book['isbn_13'] ?? null);
        $isbn13 = array_values(array_filter($isbn13, static fn (mixed $v): bool => \is_string($v) && $v !== ''));
        $rows = self::pushListRow($rows, 'isbn_13', $isbn13);

        if (isset($book['number_of_pages']) && \is_numeric($book['number_of_pages'])) {
            $rows[] = self::row('page_count', self::numberToString($book['number_of_pages']));
        }

        $languages = [];
        foreach (self::asList($book['languages'] ?? null) as $language) {
            $key = \is_array($language) ? ($language['key'] ?? null) : null;
            if (\is_string($key) && $key !== '') {
                $languages[] = basename($key);
            }
        }
        $rows = self::pushListRow($rows, 'language', $languages);

        if (\is_string($book['physical_format'] ?? null) && $book['physical_format'] !== '') {
            $rows[] = self::row('format', $book['physical_format']);
        }

        if (\is_string($book['physical_dimensions'] ?? null) && $book['physical_dimensions'] !== '') {
            $rows[] = self::row('dimensions', $book['physical_dimensions']);
        }

        $subjects = [];
        foreach (self::asList($book['subjects'] ?? null) as $subject) {
            if (\is_string($subject) && $subject !== '') {
                $subjects[] = $subject;
            }
        }
        foreach (self::asList($book['work_details'] ?? null) as $work) {
            foreach (self::asList(\is_array($work) ? ($work['subjects'] ?? null) : null) as $subject) {
                if (\is_string($subject) && $subject !== '') {
                    $subjects[] = $subject;
                }
            }
        }
        $rows = self::pushListRow($rows, 'subjects', array_values(array_unique($subjects)));

        $classifications = [];
        foreach (['lc_classifications', 'dewey_decimal_class'] as $key) {
            foreach (self::asList($book[$key] ?? null) as $classification) {
                if (\is_string($classification) && $classification !== '') {
                    $classifications[] = $classification;
                }
            }
        }
        $rows = self::pushListRow($rows, 'classifications', $classifications);

        return $rows;
    }

    // ------------------------------------------------------------------
    // Google Books (book)
    // ------------------------------------------------------------------

    /**
     * @param array<string, mixed> $volumeInfo
     *
     * @return list<DetailRow>
     */
    private static function googleBooks(array $volumeInfo): array
    {
        $rows = [];

        $authors = self::asList($volumeInfo['authors'] ?? null);
        $authors = array_values(array_filter($authors, static fn (mixed $name): bool => \is_string($name) && $name !== ''));
        $rows = self::pushListRow($rows, 'authors', $authors);

        if (\is_string($volumeInfo['publisher'] ?? null) && $volumeInfo['publisher'] !== '') {
            $rows[] = self::row('publisher', $volumeInfo['publisher']);
        }

        if (\is_string($volumeInfo['publishedDate'] ?? null) && $volumeInfo['publishedDate'] !== '') {
            $rows[] = self::row('published_date', $volumeInfo['publishedDate']);
        }

        $isbn10 = [];
        $isbn13 = [];
        foreach (self::asList($volumeInfo['industryIdentifiers'] ?? null) as $identifier) {
            $type = $identifier['type'] ?? null;
            $value = $identifier['identifier'] ?? null;

            if (!\is_string($value) || $value === '') {
                continue;
            }

            if ($type === 'ISBN_10') {
                $isbn10[] = $value;
            } elseif ($type === 'ISBN_13') {
                $isbn13[] = $value;
            }
        }
        $rows = self::pushListRow($rows, 'isbn_10', $isbn10);
        $rows = self::pushListRow($rows, 'isbn_13', $isbn13);

        if (isset($volumeInfo['pageCount']) && \is_numeric($volumeInfo['pageCount'])) {
            $rows[] = self::row('page_count', self::numberToString($volumeInfo['pageCount']));
        }

        $rows = self::pushListRow($rows, 'subjects', self::asList($volumeInfo['categories'] ?? null));

        if (\is_string($volumeInfo['language'] ?? null) && $volumeInfo['language'] !== '') {
            $rows[] = self::row('language', $volumeInfo['language']);
        }

        if (\is_string($volumeInfo['printType'] ?? null) && $volumeInfo['printType'] !== '') {
            $rows[] = self::row('format', $volumeInfo['printType']);
        }

        if (\is_array($volumeInfo['dimensions'] ?? null)) {
            $dimensionParts = [];
            foreach (['height', 'width', 'thickness'] as $dimensionKey) {
                if (\is_string($volumeInfo['dimensions'][$dimensionKey] ?? null) && $volumeInfo['dimensions'][$dimensionKey] !== '') {
                    $dimensionParts[] = $volumeInfo['dimensions'][$dimensionKey];
                }
            }

            if ($dimensionParts !== []) {
                $rows[] = self::row('dimensions', implode(' x ', $dimensionParts));
            }
        }

        return $rows;
    }

    // ------------------------------------------------------------------
    // Shared helpers
    // ------------------------------------------------------------------

    /**
     * @return DetailRow
     */
    private static function row(string $field, string $value): array
    {
        return ['key' => $field, 'label' => __("metadata.fields.{$field}"), 'value' => $value];
    }

    /**
     * @param list<DetailRow>      $rows
     * @param list<string|int>     $values
     *
     * @return list<DetailRow>
     */
    private static function pushListRow(array $rows, string $field, array $values): array
    {
        $values = array_values(array_filter($values, static fn (mixed $value): bool => $value !== null && $value !== ''));
        $values = array_values(array_unique(array_map(static fn (mixed $value): string => (string) $value, $values)));

        if ($values === []) {
            return $rows;
        }

        $rows[] = self::row($field, implode(', ', $values));

        return $rows;
    }

    /**
     * @return DetailRow
     */
    private static function dateRow(string $field, mixed $value): array
    {
        $timestamp = $value instanceof \DateTimeInterface ? $value->getTimestamp() : (int) $value;

        return self::row($field, gmdate('Y-m-d', $timestamp));
    }

    private static function numberToString(mixed $value): string
    {
        if (\is_float($value)) {
            return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
        }

        return (string) $value;
    }

    private static function boolToString(mixed $value): string
    {
        return $value ? __('metadata.fields.yes') : __('metadata.fields.no');
    }

    private static function millisecondsToDuration(int $milliseconds): string
    {
        $totalSeconds = (int) round($milliseconds / 1000);
        $minutes = intdiv($totalSeconds, 60);
        $seconds = $totalSeconds % 60;

        return \sprintf('%d:%02d', $minutes, $seconds);
    }

    /**
     * @return list<mixed>
     */
    private static function asList(mixed $value): array
    {
        if (!\is_array($value)) {
            return [];
        }

        return array_values($value);
    }

    /**
     * @param mixed $items list of associative arrays that may contain a `name` key
     *
     * @return list<string>
     */
    private static function pluckNames(mixed $items): array
    {
        $names = [];

        foreach (self::asList($items) as $item) {
            $name = \is_array($item) ? ($item['name'] ?? null) : null;

            if (\is_string($name) && $name !== '') {
                $names[] = $name;
            }
        }

        return array_values(array_unique($names));
    }

    /**
     * @param mixed $involved an IGDB `involved_companies` entry
     */
    private static function companyName(mixed $involved): ?string
    {
        if (!\is_array($involved)) {
            return null;
        }

        $company = $involved['company'] ?? null;

        if (!\is_array($company) || !\is_string($company['name'] ?? null) || $company['name'] === '') {
            return null;
        }

        return $company['name'];
    }
}

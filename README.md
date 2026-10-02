<div align="center">
    <img width="400" alt="unit3d" src="https://github.com/user-attachments/assets/2fa3678d-015c-4438-bcdb-ac6508915551" />
</div>

<p align="center">
    <a href="http://laravel.com"><img src="https://img.shields.io/badge/Laravel-12-f4645f.svg" /></a>
    <a href="https://github.com/HDInnovations/UNIT3D/blob/master/LICENSE"><img src="https://img.shields.io/badge/License-AGPL%20v3.0-yellow.svg" /></a>
    <a href="https://github.com/HDInnovations/UNIT3D/actions/workflows/lint.yml/badge.svg?branch=master"><img src="https://github.com/HDInnovations/UNIT3D/actions/workflows/lint.yml/badge.svg?branch=master" /></a>
    <a href="https://github.com/HDInnovations/UNIT3D/actions/workflows/phpunit-test.yml/badge.svg?branch=master"><img src="https://github.com/HDInnovations/UNIT3D/actions/workflows/phpunit-test.yml/badge.svg?branch=master" /></a>
    <a href="https://github.com/HDInnovations/UNIT3D/actions/workflows/compile-assets-test.yml/badge.svg?branch=master"><img src="https://github.com/HDInnovations/UNIT3D/actions/workflows/compile-assets-test.yml/badge.svg?branch=master" /></a>
    <a href="https://github.com/HDInnovations/UNIT3D/actions/workflows/larastan.yml/badge.svg?branch=master"><img src="https://github.com/HDInnovations/UNIT3D/actions/workflows/larastan.yml/badge.svg?branch=master" /></a>
    <a href="https://github.com/HDInnovations/UNIT3D/actions/workflows/prettier-blade.yml/badge.svg?branch=master"><img src="https://github.com/HDInnovations/UNIT3D/actions/workflows/prettier-blade.yml/badge.svg?branch=master" /></a>
    <a href="https://hosted.weblate.org/engage/unit3d/">
    <img src="https://hosted.weblate.org/widget/unit3d/svg-badge.svg" alt="Translation status" />
    </a>
</p>

## 📝 Table of Contents

1. [Introduction](#introduction)
2. [Installation](#installation)
3. [Updating](#updating)
4. [Documentation](#docs)
5. [Products, Services and Support](#hdinnovations)
6. [Contributing](#contributing)
7. [Translations](#translations)
8. [License](#license)


## <a name="introduction"></a> 🧐 Introduction

UNIT3D (pronounced "united") is a modern Private Torrent Tracker software built with Laravel, Livewire and AlpineJS. It offers a feature-rich platform with excellent performance, security and scalability to create and manage a private tracker. It is MySQL Strict Mode Compliant and PHP 8.4 Ready. It uses an MVC Architecture to ensure clarity between logic and presentation.

## <a name="installation"></a> 🖥️ Installation

The official script is no longer available at this time. A new one will be provided soon.

## <a name="updating"></a> 🖥️ Updating

To update your installation to the latest version, run the following command. This will pull the latest changes from the repository and update your instance:

`sudo php artisan git:update`

## <a name="docs"></a> 📚 Documentation

https://hdinnovations.github.io/UNIT3D

### Shared titles, editions and upload workflow

The catalogue lists each canonical **Work** once, with its accessible torrent variants underneath. Movies/TV group by TMDB identity (movie and TV IDs never collide), games by IGDB, music editions by MusicBrainz release-group, and Open Library editions by their shared work key. Google Books uses its volume identity. Different seasons/episodes remain labelled content scopes under the same series; they are not presented as interchangeable quality encodes. Each torrent retains its own infohash, notes, technical fields, files, seeders/leechers, completion counts and download/accounting. Identical filenames alone never merge unidentified content; explicitly choose an existing accessible title when no provider identity is available.

On `/torrents/create`, search by title, review candidates and explicitly select the correct result. Music albums offer a separately paginated edition picker showing date, country, format, labels and track counts. No first result or arbitrary music edition is selected automatically. Confirmed provider records are bound to the uploader and category with expiring server-side tokens; changing identifiers/category invalidates stale previews. Existing-title search links another variant without duplicating common metadata.

Private drafts store a whitelisted partial form snapshot, not uploaded files or trusted provider tokens. Restoring clears file inputs and requires reselecting files and confirming provider metadata. A successful publication consumes its own selected draft; failed validation leaves it intact. **Náhled / Preview** validates and renders a read-only publication review without saving a torrent, sending an announce or queuing metadata jobs. Changing the form invalidates the preview.

Catalogue filters cover music artist/label/format/release year, game platform/genre/developer, and book author/language/publisher. Edition-dependent labels, years, formats, languages and publishers match individual accessible torrent metadata, so refreshing a sibling edition cannot erase earlier matches. The expanded filter panel starts with category/type selection, then presents the applicable movie/TV, music, game or book controls. Software/other content uses shared tracker filters; XXX also offers compatible video types/resolutions. Movie-only IDs and controls are hidden for non-video categories. Switching categories clears irrelevant fields and incompatible type selections, including constraints supplied by explicit-category URLs; provider-ID links without a category remain supported. Main search includes canonical Work titles as well as filenames in SQL and Meilisearch; Meilisearch also indexes stored artist, author, developer, label and publisher names. Listing layouts group titles consistently; title details paginate all accessible variants in a labelled desktop table or labelled mobile cards, with common metadata and the complete stored source record shown once.

Staff `/staff/metadata-quality` lists missing source data, covers/descriptions and provider errors. Music's absent annotation is expected, not a metadata-quality failure. Refresh queries that title's own provider, displays old/new fields, then applies only explicitly confirmed selections. Reviews expire, are owner/title-bound and single-use, and reject changes made since preview. Refreshing common metadata does not overwrite uploader notes or torrent technical fields.

Deployment: apply additive migrations (`php artisan migrate --force`), run the local-only `php artisan media:backfill-works`, refresh configuration/routes/views, run `php artisan scout:sync-index-settings` and `php artisan scout:import 'App\Models\Torrent'`, rebuild assets, and restart queue/scheduler services. Backfill attaches existing rows without provider requests; legacy caches without raw records cannot erase a complete Work snapshot. Existing incomplete source records can be refreshed from the staff review page.

### Upload metadata lookup

Alternatively, choose a category, enter its identifier, then click **Získat metadata / Fetch metadata**. Movies and TV use TMDB IDs, games use IGDB IDs, music uses a MusicBrainz release or release-group UUID, and books accept ISBN-10/ISBN-13 or an Open Library edition ID. Software, other content, and XXX do not offer provider lookup. The preview shows its source and actual description language. Empty titles are filled; replacing an existing description requires confirmation. Lookup does not publish the torrent.

The creation form shows a category-specific **Content metadata** fieldset before any lookup, including genres, publishers, year, and game engines where applicable. These are read-only provider fields, populated by Fetch metadata; every additional readable value returned by the lookup is shown as another field. Changing the category or identifier hides unrelated previously fetched values. The lookup also returns a localized metadata table for saved details and an expandable complete source record. Games include release date/year, genres, developers and publishers distinguished by provider roles, engines, platforms, themes, modes, perspectives, age ratings, language support, ratings, companies, artwork, videos, websites, and related releases. Movies/TV include the full detail response with credits, external IDs, keywords, images, videos, release dates/content ratings, and recommendations. Music includes labels/catalogue numbers, genres/tags, release information, and tracks across all discs; books include authors, publication details, ISBNs, subjects, classifications, and linked Open Library work/author records when available. Absent metadata is omitted, not invented. Reference expansion is bounded; full source data means the obtained record, not recursively downloading the provider's entire catalogue.

Complete game/movie/TV source records are retained in JSON on their existing metadata models; book/music records use `torrent_metadata.raw`. Saved torrent and similar-content pages expose the same readable summary and full source data. Existing records gain the extra fields on their next metadata refresh; they are not populated by the schema migration alone. Apply the additive migration, rebuild frontend assets, refresh compiled views, and restart queue workers when deploying this change.

Books check exact ISBN matches in Google Books for Czech annotations and fall back to Open Library edition/work descriptions. A Czech edition does not make an unlabelled shared work description Czech. Missing Czech annotations produce a warning rather than a fabricated or silently relabelled translation. MusicBrainz provides music metadata and artwork, not book-style annotations.

Music accepts both specific editions (`release`) and albums/singles (`release-group`) through the `musicbrainz_id` upload field and lookup response identifier. When a release is absent, the same UUID is checked as a release-group; provider outages are not treated as missing releases. Album metadata includes artists, first release date/year, and available genres/tags, with album-level source/artwork links. No arbitrary edition is selected: labels, format, catalogue numbers, and tracklists require a specific release ID. The saved album retains its original source record, and an unavailable edition track count remains null rather than zero. Refresh compiled views and restart queue workers when deploying this audio fix; it requires no additional migration.

Provider credentials stay on the server: `TMDB_API_KEY` for TMDB; `TWITCH_CLIENT_ID` and `TWITCH_CLIENT_SECRET` for IGDB. `GOOGLE_BOOKS_API_KEY` is optional, but anonymous Google Books requests can exhaust the shared quota (HTTP 429); configure a key with available quota for reliable Czech book lookup. Open Library and MusicBrainz require no API key. After changing production environment settings, refresh Laravel's configuration cache.

## <a name="hdinnovations"></a> 🛠️ Products, Services and Support

HDInnovations offers a variety of services to help you with your UNIT3D instance. We offer services such as installation, updating, server tuning, dependency tuning, themes, porting from different codebase and more. We have a Discord server for support and general discussion. This is a private server, and you will need to be invited to join. There is a small fee to join the server to help support the development of UNIT3D.

https://hdinnovations.github.io/HDInnovations

## <a name="contributing"></a> 🤝 Contributing

Please read [CONTRIBUTING.md](https://github.com/HDInnovations/UNIT3D/blob/master/CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests to us. A massive thank you to all of our <a href="https://github.com/HDInnovations/UNIT3D/graphs/contributors">contributors</a>.

## <a name="translations"></a> 🌎 Translations

Vltava defaults to Czech (`cs`) with English (`en`) as the fallback for missing translations in other locales. An explicit `?lang=` override takes precedence over the account language, then the guest session language; request overrides do not change saved preferences. Invalid or non-string locale values fall back to the configured default.

Keep action labels separate from transfer totals and completed states: `common.upload-action` / `common.download-action`, `common.uploaded` / `common.downloaded`. Translate complete sentences with named placeholders, not English grammar fragments. Use the existing `interface`, `member-interface`, `media-interface`, `livewire-interface`, `staff-interface`, and `application-messages` catalogues for first-party text. JavaScript receives escaped JSON from Blade; generated CSS labels use localized `data-label` attributes.

Notifications and queued mail use the recipient's account language. Shared chat and IRC broadcasts use the site's default language. User-authored content, metadata titles, protocol values, and identifiers are not translated. Achievement descriptions must be rendered with `App\Achievements\Achievement::descriptionFor()`; do not instantiate achievements for presentation, because the vendor constructor synchronizes shared database metadata.

Run `docker compose exec -T laravel.test php scripts/audit-i18n.php` to check literal translation references (including achievement description constants), Czech catalogue completeness, duplicate English/Czech keys, and matching named placeholders. It exits nonzero on defects and does not boot Laravel or access the database. This structural check does not replace reviewing translation meaning or exercising dynamic translation paths.

We use Weblate for translations. You can easily contribute to translations at https://hosted.weblate.org/engage/unit3d/. Use the following graphic to see if your native language could use some work.

<a href="https://hosted.weblate.org/engage/unit3d/">
<img src="https://hosted.weblate.org/widget/unit3d/horizontal-auto.svg" alt="Translation status" />
</a>

## <a name="license"></a> 📜 License

This project is licensed under the AGPL v3.0 License. See the [LICENSE](https://github.com/HDInnovations/UNIT3D/blob/master/LICENSE.md) file for details.



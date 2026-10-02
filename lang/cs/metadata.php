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
        'identifier-required'      => 'Pro načtení metadat je třeba zadat identifikátor.',
        'category-not-lookupable'  => 'Tato kategorie nepodporuje vyhledávání metadat.',
        'invalid-numeric-id'       => 'Zadejte platné číselné ID.',
        'invalid-isbn'             => 'Zadejte platné ISBN-10 nebo ISBN-13, nebo ID edice Open Library (např. OL7353617M).',
        'invalid-musicbrainz-id'   => 'Zadejte platné UUID vydání MusicBrainz.',
        'not-found'                => 'Pro tento identifikátor nebyla nalezena žádná metadata.',
        'provider-unavailable'     => 'Poskytovatel metadat je momentálně nedostupný. Zkuste to prosím později.',
        'tmdb-not-configured'      => 'Vyhledávání metadat přes TMDB není na tomto serveru nakonfigurováno.',
        'igdb-not-configured'      => 'Vyhledávání metadat přes IGDB není na tomto serveru nakonfigurováno.',
        'selection-expired' => 'Výběr metadat vypršel nebo nepatří tomuto formuláři. Načtěte metadata znovu.',
        'selection-mismatch' => 'Vybraná metadata neodpovídají zadanému identifikátoru. Načtěte metadata znovu.',
        'work-mismatch' => 'Vybraný titul neodpovídá kategorii nebo identifikátoru tohoto torrentu.',
        'draft-unavailable' => 'Tento rozpracovaný upload není dostupný pro váš účet.',
    ],

    'warnings' => [
        'tmdb-english-fallback'      => 'Pro tento titul není na TMDB k dispozici český popis; zobrazuje se anglický popis.',
        'book-no-czech-description' => 'Pro tuto knihu není k dispozici ověřený český popis; zobrazuje se popis v původním jazyce, pokud existuje.',
        'book-no-description'       => 'Toto české vydání nemá k dispozici žádný popis.',
    ],

    'sections' => [
        'summary'       => 'Souhrn metadat',
        'form'          => 'Metadata obsahu',
        'form_hint'     => 'Pole pouze pro čtení. Zadejte identifikátor a klikněte na Získat metadata; dostupné údaje se vyplní přímo zde.',
        'not_loaded'    => 'Vyplní se po načtení metadat',
        'unavailable'   => 'Poskytovatel údaj neuvádí',
        'advanced'      => 'Úplná data poskytovatele',
        'show_advanced' => 'Zobrazit úplná data poskytovatele',
        'hide_advanced' => 'Skrýt úplná data poskytovatele',
    ],

    'fields' => [
        'yes'                   => 'Ano',
        'no'                    => 'Ne',
        'minutes'               => ':count min',
        'disc'                  => 'Disk :number',

        // Shared
        'year'                  => 'Rok',
        'release_date'          => 'Datum vydání',
        'genres'                => 'Žánry',
        'languages'             => 'Jazyky',
        'language'              => 'Jazyk',
        'rating'                => 'Hodnocení',
        'rating_count'          => 'Počet hodnocení',
        'status'                => 'Stav',
        'country'               => 'Země',
        'publisher'             => 'Vydavatel',
        'published_date'        => 'Datum publikace',
        'format'                => 'Formát',
        'formats'               => 'Formáty',
        'dimensions'            => 'Rozměry',
        'subjects'              => 'Témata',
        'page_count'            => 'Počet stran',
        'isbn_10'                => 'ISBN-10',
        'isbn_13'                => 'ISBN-13',
        'classifications'       => 'Klasifikace',

        // IGDB (game)
        'developers'            => 'Vývojáři',
        'publishers'            => 'Vydavatelé',
        'porting_companies'     => 'Portovací studia',
        'supporting_companies'  => 'Podpůrná studia',
        'game_engines'          => 'Herní enginy',
        'platforms'             => 'Platformy',
        'themes'                => 'Témata hry',
        'game_modes'            => 'Herní režimy',
        'player_perspectives'   => 'Pohled hráče',
        'storyline'             => 'Příběh',
        'franchise'             => 'Franšíza',
        'collection'            => 'Kolekce',
        'similar_games'         => 'Podobné hry',
        'age_ratings'           => 'Věkové hodnocení',
        'aggregated_rating'     => 'Kritické hodnocení',
        'aggregated_rating_count' => 'Počet kritických hodnocení',
        'multiplayer'                     => 'Multiplayer',
        'multiplayer_campaign_coop'       => 'Kooperace v kampani',
        'multiplayer_drop_in'             => 'Připojení za běhu',
        'multiplayer_lan_coop'            => 'LAN kooperace',
        'multiplayer_offline_coop'        => 'Offline kooperace',
        'multiplayer_online_coop'         => 'Online kooperace',
        'multiplayer_split_screen'        => 'Dělená obrazovka',
        'multiplayer_online_split_screen' => 'Online dělená obrazovka',
        'multiplayer_offline_max'         => 'Max. hráčů offline',
        'multiplayer_online_max'          => 'Max. hráčů online',
        'websites'              => 'Webové stránky',

        // TMDB (movie/tv)
        'original_title'        => 'Originální název',
        'original_language'     => 'Originální jazyk',
        'tagline'                => 'Slogan',
        'runtime'                => 'Délka',
        'budget'                 => 'Rozpočet',
        'revenue'                => 'Tržby',
        'companies'              => 'Produkční společnosti',
        'countries'              => 'Země produkce',
        'spoken_languages'       => 'Mluvené jazyky',
        'networks'               => 'Televizní sítě',
        'seasons'                => 'Řady',
        'episodes'               => 'Díly',
        'creators'               => 'Tvůrci',
        'directors'              => 'Režiséři',
        'cast'                   => 'Obsazení',
        'adult'                  => 'Obsah pro dospělé',
        'homepage'               => 'Domovská stránka',
        'imdb_id'                => 'IMDb ID',
        'tvdb_id'                => 'TVDB ID',
        'keywords'               => 'Klíčová slova',
        'alternative_titles'     => 'Alternativní názvy',
        'recommendations'        => 'Doporučení',
        'videos'                 => 'Videa',
        'certification'          => 'Certifikace',

        // MusicBrainz (music)
        'artists'                => 'Interpreti',
        'labels'                 => 'Vydavatelství',
        'catalog_numbers'        => 'Katalogová čísla',
        'barcode'                => 'Čárový kód',
        'packaging'              => 'Balení',
        'tags'                   => 'Štítky',
        'tracklist'              => 'Seznam skladeb',

        // Books
        'authors'                => 'Autoři',
    ],
];

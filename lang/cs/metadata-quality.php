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
    'nav' => [
        'link' => 'Kvalita metadat',
    ],

    'kinds' => [
        'movie' => 'Film',
        'tv'    => 'Seriál',
        'game'  => 'Hra',
        'music' => 'Hudba',
        'book'  => 'Kniha',
    ],

    'index' => [
        'title'                 => 'Kvalita metadat',
        'heading'               => 'Kvalita metadat titulů',
        'filter-kind'           => 'Druh',
        'filter-kind-any'       => 'Libovolný druh',
        'filter-title'          => 'Název',
        'filter-title-placeholder' => 'Hledat název…',
        'filter-status'         => 'Stav',
        'filter-submit'         => 'Filtrovat',
        'column-title'          => 'Název',
        'column-kind'           => 'Druh',
        'column-source'         => 'Zdroj',
        'column-status'         => 'Stav',
        'column-updated'        => 'Naposledy obnoveno',
        'column-actions'        => 'Akce',
        'never-refreshed'       => 'Nikdy neobnoveno',
        'action-preview'        => 'Náhled obnovení',
        'no-works'              => 'Žádné tituly neodpovídají zvoleným filtrům.',
    ],

    'status' => [
        'all'                 => 'Vše',
        'missing_raw'         => 'Chybí surová data',
        'missing_cover'       => 'Chybí obálka',
        'missing_description' => 'Chybí popis',
        'error'               => 'Chyba poskytovatele',
        'ok'                  => 'V pořádku',
    ],

    'preview' => [
        'title'                 => 'Náhled obnovení',
        'heading'               => 'Náhled obnovení: :title',
        'intro'                 => 'Toto je čerstvé vyhledání pouze pro čtení vůči vlastnímu zdroji titulu. Nic se neuloží, dokud níže nevyberete pole a nepotvrdíte.',
        'field-title'           => 'Název',
        'field-description'     => 'Popis',
        'field-cover_url'       => 'Obálka',
        'field-raw'             => 'Surová data poskytovatele',
        'current-value'         => 'Aktuální',
        'new-value'             => 'Nové',
        'no-value'              => '(žádné)',
        'unchanged'              => 'Beze změny',
        'expected-empty-music'  => 'Prázdná hodnota je zde očekávaná: tento poskytovatel neuvádí textový popis u hudebních vydání.',
        'select-field'          => 'Použít toto pole',
        'confirm'               => 'Použít vybraná pole',
        'confirm-message'       => 'Použít vybraná pole na sdílená metadata tohoto titulu? Tuto akci nelze vrátit zpět.',
        'back'                  => 'Zpět na přehled',
        'cover-preview-alt'     => 'Náhled obálky',
    ],

    'messages' => [
        'applied' => 'Metadata titulu „:title“ byla obnovena z vybraných polí.',
    ],

    'errors' => [
        'no-refreshable-source'  => 'Tento titul nemá zdroj u poskytovatele k obnovení; jde o dočasnou nebo ručně vytvořenou identitu.',
        'no-compatible-torrent'  => 'Žádný nesmazaný torrent navázaný na tento titul nemá kategorii odpovídající jeho druhu, zdrojovou kategorii tak nelze určit.',
        'source-not-found'       => 'Poskytovatel už nemá záznam pro zdrojový identifikátor tohoto titulu.',
        'source-unavailable'     => 'Poskytovatel metadat je momentálně nedostupný. Zkuste to prosím později.',
        'source-unsupported'     => 'Uložený zdrojový identifikátor tohoto titulu už není podporovaný formát pro vyhledání.',
        'preview-stale'          => 'Platnost tohoto náhledu vypršela, byl již použit, nebo už neodpovídá aktuálním metadatům titulu. Vytvořte náhled obnovení znovu.',
        'select-at-least-one-field' => 'Vyberte alespoň jedno pole k použití.',
    ],
];

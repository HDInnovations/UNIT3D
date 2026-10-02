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
    'preview' => [
        'aria-label'          => 'Náhled nahrávaného torrentu',
        'button'              => 'Náhled',
        'button-loading'      => 'Připravuji náhled…',
        'heading'             => 'Náhled',
        'hint'                => 'Prohlédněte si torrent přesně tak, jak bude zveřejněn. Zatím se nic neukládá ani neoznamuje; formulář výše upravte a náhled si vytvořte znovu.',
        'error-heading'       => 'Náhled se nepodařilo vytvořit',
        'generic-error'       => 'Náhled se nepodařilo vytvořit. Zkuste to prosím znovu.',
        'folder-name'         => 'Složka torrentu',
        'scope-label'         => 'Rozsah obsahu',
        'scope-complete'      => 'Kompletní řada',
        'scope-season'        => 'Řada :season',
        'scope-episode'       => 'Řada :season, díl :episode',
        'file-count-label'    => 'Soubory',
        'visibility-label'    => 'Viditelnost',
        'visibility-anon'     => 'Anonymní nahrání',
        'visibility-named'    => 'Přiřazeno k vašemu účtu',
        'visibility-mod-queue' => 'Odesláno do moderátorské fronty',
    ],

    'drafts' => [
        'heading'                => 'Koncepty',
        'hint'                   => 'Uložte si rozpracované nahrání pod názvem a vraťte se k němu později. Koncepty jsou soukromé, vidíte je jen vy.',
        'name-label'             => 'Název konceptu',
        'name-placeholder'       => 'např. „Ano, šéfe! S01E04“',
        'save-new-button'        => 'Uložit jako nový koncept',
        'save-button'            => 'Aktualizovat koncept',
        'list-label'             => 'Vaše uložené koncepty',
        'empty'                  => 'Zatím nemáte žádné uložené koncepty.',
        'restore-button'         => 'Obnovit',
        'delete-button'          => 'Smazat',
        'delete-confirm-title'   => 'Smazat tento koncept?',
        'delete-confirm-text'    => 'Tuto akci nelze vrátit zpět.',
        'restore-confirm-title'  => 'Obnovit tento koncept?',
        'restore-confirm-text'   => 'Tím se přepíší pole aktuálně vyplněná v tomto formuláři.',
        'restore-files-notice'   => 'Koncepty nikdy neukládají soubory: než budete zveřejňovat, znovu vyberte soubor .torrent, NFO, obal a banner.',
        'saved-status'           => 'Koncept uložen.',
        'restored-status'        => 'Koncept obnoven.',
        'deleted-status'         => 'Koncept smazán.',
        'loading'                => 'Načítání…',
        'generic-error'          => 'S koncepty se něco pokazilo. Zkuste to prosím znovu.',
    ],

    'discovery' => [
        'heading'                 => 'Vyhledat titul',
        'hint'                    => 'Najděte správný titul z externích zdrojů a poté výběrem výsledku načtěte jeho metadata.',
        'query-label'             => 'Hledaný výraz',
        'search-button'           => 'Hledat',
        'searching'               => 'Hledám…',
        'no-results'              => 'Nic nenalezeno.',
        'generic-error'           => 'Vyhledávání se nepodařilo dokončit. Zkuste to prosím znovu.',
        'pick-button'             => 'Použít tento výsledek',
        'results-label'           => 'Výsledky hledání',
        'browse-editions-button'  => 'Procházet vydání',
        'use-as-album-button'     => 'Použít tuto skupinu vydání tak, jak je',
        'editions-heading'        => 'Vydání této skupiny',
        'editions-loading'        => 'Načítám vydání…',
        'editions-empty'          => 'Pro tuto skupinu vydání nebyla nalezena žádná konkrétní vydání.',
        'editions-load-more'      => 'Načíst další vydání',
        'editions-back'           => 'Zpět na výsledky hledání',
        'track-count'             => ':count skladeb',
    ],

    'work-search' => [
        'heading'               => 'Přidat k existujícímu titulu',
        'hint'                  => 'Vyhledejte titul, který už na trackeru existuje.',
        'grouping-hint'         => 'Výběrem existujícího titulu přidáte toto nahrání jako novou kvalitativní variantu k němu, místo vytvoření duplicitního záznamu v katalogu.',
        'query-label'           => 'Hledání titulu',
        'search-button'         => 'Hledat',
        'searching'             => 'Hledám…',
        'no-results'            => 'Nebyly nalezeny žádné odpovídající tituly.',
        'generic-error'         => 'Vyhledávání se nepodařilo dokončit. Zkuste to prosím znovu.',
        'select-button'         => 'Vybrat',
        'selected-label'        => 'Vybraný existující titul',
        'selected-restored-label' => 'Existující titul #:id (obnoveno)',
        'clear-button'          => 'Zrušit výběr',
        'clear-status'          => 'Výběr existujícího titulu byl zrušen.',
    ],
];

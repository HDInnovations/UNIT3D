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
        'aria-label'          => 'Upload preview',
        'button'              => 'Preview',
        'button-loading'      => 'Building preview…',
        'heading'             => 'Preview',
        'hint'                => 'Review the torrent exactly as it will be published. Nothing is saved or announced yet; edit the form above and preview again as needed.',
        'error-heading'       => 'The preview could not be built',
        'generic-error'       => 'The preview could not be built. Please try again.',
        'folder-name'         => 'Torrent folder',
        'scope-label'         => 'Content scope',
        'scope-complete'      => 'Complete series',
        'scope-season'        => 'Season :season',
        'scope-episode'       => 'Season :season, Episode :episode',
        'file-count-label'    => 'Files',
        'visibility-label'    => 'Visibility',
        'visibility-anon'     => 'Anonymous upload',
        'visibility-named'    => 'Credited to your account',
        'visibility-mod-queue' => 'Sent to the moderation queue',
    ],

    'drafts' => [
        'heading'                => 'Drafts',
        'hint'                   => 'Save your in-progress upload under a name and come back to it later. Drafts are private to your account.',
        'name-label'             => 'Draft name',
        'name-placeholder'       => 'e.g. "Ano, šéfe! S01E04"',
        'save-new-button'        => 'Save as new draft',
        'save-button'            => 'Update draft',
        'list-label'             => 'Your saved drafts',
        'empty'                  => 'You have no saved drafts yet.',
        'restore-button'         => 'Restore',
        'delete-button'          => 'Delete',
        'delete-confirm-title'   => 'Delete this draft?',
        'delete-confirm-text'    => 'This cannot be undone.',
        'restore-confirm-title'  => 'Restore this draft?',
        'restore-confirm-text'   => 'This replaces the fields currently filled in on this form.',
        'restore-files-notice'   => 'Drafts never store files: please reselect the .torrent, NFO, cover, and banner files again before publishing.',
        'saved-status'           => 'Draft saved.',
        'restored-status'        => 'Draft restored.',
        'deleted-status'         => 'Draft deleted.',
        'loading'                => 'Loading…',
        'generic-error'          => 'Something went wrong with drafts. Please try again.',
    ],

    'discovery' => [
        'heading'                 => 'Search for the title',
        'hint'                    => 'Find the correct title from external sources, then pick a result to fetch its metadata.',
        'query-label'             => 'Search query',
        'search-button'           => 'Search',
        'searching'               => 'Searching…',
        'no-results'              => 'No matches found.',
        'generic-error'           => 'The search could not be completed. Please try again.',
        'pick-button'             => 'Use this result',
        'results-label'           => 'Search results',
        'browse-editions-button'  => 'Browse editions',
        'use-as-album-button'     => 'Use this release group as-is',
        'editions-heading'        => 'Editions of this release group',
        'editions-loading'        => 'Loading editions…',
        'editions-empty'          => 'No specific editions were found for this release group.',
        'editions-load-more'      => 'Load more editions',
        'editions-back'           => 'Back to search results',
        'track-count'             => ':count tracks',
    ],

    'work-search' => [
        'heading'               => 'Add to an existing title',
        'hint'                  => 'Search for a title already on the tracker.',
        'grouping-hint'         => 'Selecting an existing title adds this upload as a new quality variant of it, instead of creating a duplicate catalogue entry.',
        'query-label'           => 'Title search',
        'search-button'         => 'Search',
        'searching'             => 'Searching…',
        'no-results'            => 'No matching titles found.',
        'generic-error'         => 'The search could not be completed. Please try again.',
        'select-button'         => 'Select',
        'selected-label'        => 'Selected existing title',
        'selected-restored-label' => 'Existing title #:id (restored)',
        'clear-button'          => 'Clear selection',
        'clear-status'          => 'Existing title selection cleared.',
    ],
];

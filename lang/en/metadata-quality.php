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
        'link' => 'Metadata quality',
    ],

    'kinds' => [
        'movie' => 'Movie',
        'tv'    => 'TV',
        'game'  => 'Game',
        'music' => 'Music',
        'book'  => 'Book',
    ],

    'index' => [
        'title'                 => 'Metadata quality',
        'heading'               => 'Work metadata quality',
        'filter-kind'           => 'Kind',
        'filter-kind-any'       => 'Any kind',
        'filter-title'          => 'Title',
        'filter-title-placeholder' => 'Search titles…',
        'filter-status'         => 'Status',
        'filter-submit'         => 'Filter',
        'column-title'          => 'Title',
        'column-kind'           => 'Kind',
        'column-source'         => 'Source',
        'column-status'         => 'Status',
        'column-updated'        => 'Last refreshed',
        'column-actions'        => 'Actions',
        'never-refreshed'       => 'Never refreshed',
        'action-preview'        => 'Preview refresh',
        'no-works'              => 'No Works match these filters.',
    ],

    'status' => [
        'all'                 => 'All',
        'missing_raw'         => 'Missing raw data',
        'missing_cover'       => 'Missing cover',
        'missing_description' => 'Missing description',
        'error'               => 'Provider error',
        'ok'                  => 'OK',
    ],

    'preview' => [
        'title'                 => 'Review refresh',
        'heading'               => 'Review refresh: :title',
        'intro'                 => 'This is a fresh, read-only lookup against the Work\'s own source. Nothing is saved until you select fields below and confirm.',
        'field-title'           => 'Title',
        'field-description'     => 'Description',
        'field-cover_url'       => 'Cover',
        'field-raw'             => 'Raw provider data',
        'current-value'         => 'Current',
        'new-value'             => 'New',
        'no-value'              => '(none)',
        'unchanged'              => 'Unchanged',
        'expected-empty-music'  => 'Empty is expected here: this provider does not supply prose descriptions for music releases.',
        'select-field'          => 'Apply this field',
        'confirm'               => 'Apply selected fields',
        'confirm-message'       => 'Apply the selected fields to this Work\'s shared metadata? This cannot be undone.',
        'back'                  => 'Back to dashboard',
        'cover-preview-alt'     => 'Cover preview',
    ],

    'messages' => [
        'applied' => 'Metadata for ":title" was refreshed from the selected fields.',
    ],

    'errors' => [
        'no-refreshable-source'  => 'This Work has no provider source to refresh; it is a provisional or manually created identity.',
        'no-compatible-torrent'  => 'No non-deleted torrent linked to this Work has a category matching its kind, so no source category could be determined.',
        'source-not-found'       => 'The provider no longer has a record for this Work\'s source identifier.',
        'source-unavailable'     => 'The metadata provider is currently unavailable. Please try again later.',
        'source-unsupported'     => 'This Work\'s stored source identifier is no longer a supported lookup format.',
        'preview-stale'          => 'This preview has expired, was already applied, or no longer matches the Work\'s current metadata. Preview the refresh again.',
        'select-at-least-one-field' => 'Select at least one field to apply.',
    ],
];

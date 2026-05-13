<?php

declare(strict_types=1);

use App\Models\Torrent;

test('thank button can render compact icon-only style for torrent rows', function (): void {
    $torrent = Torrent::factory()->make();
    $torrent->setAttribute('thanks_count', 3);

    $html = view('livewire.thank-button', [
        'iconOnly' => true,
        'torrent'  => $torrent,
    ])->render();

    expect($html)->toContain('form__standard-icon-button')
        ->and($html)->toContain('fa-heart')
        ->and($html)->toContain('title="Thank (3)"')
        ->and($html)->toContain('aria-label="Thank (3)"')
        ->and($html)->not->toContain('form__button--outlined');
});

test('thank button keeps the full torrent-page label by default', function (): void {
    $torrent = Torrent::factory()->make();
    $torrent->setAttribute('thanks_count', 5);

    $html = view('livewire.thank-button', [
        'iconOnly' => false,
        'torrent'  => $torrent,
    ])->render();

    expect($html)->toContain('form__button--outlined')
        ->and($html)->toContain('Thank (5)')
        ->and($html)->not->toContain('form__standard-icon-button');
});

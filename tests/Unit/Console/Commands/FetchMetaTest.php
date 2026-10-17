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
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

/**
 * @see App\Console\Commands\FetchMeta
 */
it('fetches all types when no option provided', function (): void {
    $this->artisan('fetch:meta')
        ->expectsOutput('Querying all tmdb movie ids')
        ->expectsOutput('Querying all tmdb tv ids')
        ->expectsOutput('Querying all igdb game ids')
        ->assertExitCode(0);
});

it('fetches only movies when movie specified', function (): void {
    $this->artisan('fetch:meta', ['--only' => 'movie'])
        ->expectsOutput('Querying all tmdb movie ids')
        ->doesntExpectOutput('Querying all tmdb tv ids')
        ->doesntExpectOutput('Querying all igdb game ids')
        ->assertExitCode(0);
});

it('fetches only tv when tv specified', function (): void {
    $this->artisan('fetch:meta', ['--only' => 'tv'])
        ->doesntExpectOutput('Querying all tmdb movie ids')
        ->expectsOutput('Querying all tmdb tv ids')
        ->doesntExpectOutput('Querying all igdb game ids')
        ->assertExitCode(0);
});

it('fetches only games when game specified', function (): void {
    $this->artisan('fetch:meta', ['--only' => 'game'])
        ->doesntExpectOutput('Querying all tmdb movie ids')
        ->doesntExpectOutput('Querying all tmdb tv ids')
        ->expectsOutput('Querying all igdb game ids')
        ->assertExitCode(0);
});

it('fetches multiple types when specified', function (): void {
    $this->artisan('fetch:meta', ['--only' => 'movie,tv'])
        ->expectsOutput('Querying all tmdb movie ids')
        ->expectsOutput('Querying all tmdb tv ids')
        ->doesntExpectOutput('Querying all igdb game ids')
        ->assertExitCode(0);
});

it('handles whitespace in types', function (): void {
    $this->artisan('fetch:meta', ['--only' => ' movie , tv '])
        ->expectsOutput('Querying all tmdb movie ids')
        ->expectsOutput('Querying all tmdb tv ids')
        ->doesntExpectOutput('Querying all igdb game ids')
        ->assertExitCode(0);
});

it('ignores invalid types', function (): void {
    $this->artisan('fetch:meta', ['--only' => 'invalid'])
        ->doesntExpectOutput('Querying all tmdb movie ids')
        ->doesntExpectOutput('Querying all tmdb tv ids')
        ->doesntExpectOutput('Querying all igdb game ids')
        ->assertExitCode(0);
});

it('processes valid types and ignores invalid ones', function (): void {
    $this->artisan('fetch:meta', ['--only' => 'movie,invalid,tv'])
        ->expectsOutput('Querying all tmdb movie ids')
        ->expectsOutput('Querying all tmdb tv ids')
        ->doesntExpectOutput('Querying all igdb game ids')
        ->assertExitCode(0);
});

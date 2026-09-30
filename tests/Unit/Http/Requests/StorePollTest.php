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

use App\Http\Requests\StorePoll;

beforeEach(function (): void {
    $this->subject = new StorePoll();
});

test('authorize', function (): void {
    $actual = $this->subject->authorize();

    expect($actual)->toBeTrue();
});

test('rules', function (): void {
    $actual = $this->subject->rules();

    $this->assertValidationRules([
        'title' => [
            'required',
            'min:10',
        ],
        'multiple_choice' => [
            'required',
            'boolean',
        ],
        'options.*.name' => [
            'required',
            'max:255',
        ],
        'options' => [
            'array',
            'min:2',
            'max:20',
        ],
    ], $actual);
});

test('messages', function (): void {
    $actual = $this->subject->messages();

    expect($actual)->toEqual([
        'options.*.required' => trans('application-messages.validation.poll-options-required'),
    ]);
});

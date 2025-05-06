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

use App\Http\Requests\Staff\StoreMassPrivateMessageRequest;

beforeEach(function (): void {
    $this->subject = new StoreMassPrivateMessageRequest();
});

test('authorize', function (): void {
    $actual = $this->subject->authorize();

    expect($actual)->toBeTrue();
});

test('rules', function (): void {
    $actual = $this->subject->rules();

    expect($actual)->toHaveKeys(['groups', 'groups.*', 'subject', 'message'])
        ->and($actual['groups'])->toBe(['required', 'array'])
        ->and($actual['groups.*'])->toBe(['exists:groups,id'])
        ->and($actual['subject'])->toBe(['required', 'string', 'max:255'])
        ->and($actual['message'])->toBe(['required', 'string', 'max:65536']);
});

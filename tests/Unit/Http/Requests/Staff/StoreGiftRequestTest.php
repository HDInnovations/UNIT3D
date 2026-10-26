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

use App\Http\Requests\Staff\StoreGiftRequest;

beforeEach(function (): void {
    $this->subject = new StoreGiftRequest();
});

test('rules', function (): void {
    $actual = $this->subject->rules();

    $this->assertValidationRules([
        'username' => [
            'required',
            'exists:users,username',
            'max:180',
        ],
        'seedbonus' => [
            'required',
            'integer',
            'min:0',
        ],
        'invites' => [
            'required',
            'integer',
            'min:0',
        ],
        'fl_tokens' => [
            'required',
            'integer',
            'min:0',
        ],
    ], $actual);
});

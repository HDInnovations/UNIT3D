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

use App\Http\Requests\StoreTicketRequest;

beforeEach(function (): void {
    $this->subject = new StoreTicketRequest();
});

test('rules', function (): void {
    $actual = $this->subject->rules();

    $this->assertValidationRules([
        'category_id' => [
            'required',
            'integer',
        ],
        'priority_id' => [
            'required',
            'integer',
        ],
        'subject' => [
            'required',
            'max:255',
        ],
        'body' => [
            'required',
            'max:65535',
        ],
    ], $actual);
});

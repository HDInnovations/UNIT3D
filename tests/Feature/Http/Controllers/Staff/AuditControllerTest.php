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

use App\Http\Livewire\AuditLogSearch;
use App\Models\Audit;
use App\Models\Group;
use App\Models\User;

beforeEach(function (): void {
    $this->staffUser = User::factory()->create([
        'group_id' => fn () => Group::factory()->create([
            'is_owner' => true,
            'is_admin' => true,
            'is_modo'  => true,
        ])->id,
    ]);
});

test('destroy returns an ok response', function (): void {
    $audit = Audit::factory()->create();

    $response = $this->actingAs($this->staffUser)->delete(route('staff.audits.destroy', [$audit]));
    $response->assertRedirect(route('staff.audits.index'))->assertSessionHas('success', 'Audit record has successfully been deleted');

    $this->assertModelMissing($audit);
});

test('index returns an ok response', function (): void {
    $response = $this->actingAs($this->staffUser)->get(route('staff.audits.index'));
    $response->assertOk();
    $response->assertViewIs('Staff.audit.index');
    $response->assertSeeLivewire(AuditLogSearch::class);
});

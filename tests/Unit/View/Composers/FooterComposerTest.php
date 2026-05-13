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

use App\Models\Page;
use App\View\Composers\FooterComposer;
use Illuminate\View\View;

test('footer composer orders pages by configured footer position', function (): void {
    cache()->forget('cached-footer-pages');

    $thirdPage = Page::factory()->create(['footer_position' => 30]);
    $firstPage = Page::factory()->create(['footer_position' => 10]);
    $secondPage = Page::factory()->create(['footer_position' => 20]);

    $view = Mockery::mock(View::class);

    $view->shouldReceive('with')
        ->once()
        ->withArgs(function (array $data) use ($firstPage, $secondPage, $thirdPage): bool {
            expect($data['pages']->pluck('id')->all())
                ->toBe([$firstPage->id, $secondPage->id, $thirdPage->id]);

            return true;
        });

    (new FooterComposer())->compose($view);
});

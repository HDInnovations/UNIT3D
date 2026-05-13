<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\Models\Page;
use Illuminate\View\View;

class FooterComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $view->with([
            'pages' => cache()->flexible(
                'cached-footer-pages',
                [3600, 3600 * 2],
                fn () => Page::select(['id', 'name', 'footer_position', 'created_at'])
                    ->orderBy('footer_position')
                    ->orderBy('id')
                    ->take(6)
                    ->get()
            ),
        ]);
    }
}

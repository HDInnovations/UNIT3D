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
 * @author     Roardom <roardom@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

$resourceDirectory = dirname(__DIR__, 2).'/resources';
$viewPaths = [];

foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($resourceDirectory.'/views')) as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
        $viewPaths[] = [mb_substr($file->getPathname(), mb_strlen($resourceDirectory))];
    }
}

test('views must be kebab case', function (string $viewPath): void {
    // Partials are still allowed to be prefixed with an underscore
    expect($viewPath)->toMatch('/^\/views\/(?:[0-9a-zA-Z-]|\/_?)+\.blade\.php$/');
})->with($viewPaths);

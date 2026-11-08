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

namespace App\Exceptions;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Override;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exceptions with their corresponding custom log levels.
     *
     * @var array<class-string<Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        \Illuminate\Queue\MaxAttemptsExceededException::class,
        MetaFetchNotFoundException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    #[Override]
    public function register(): void
    {
        $this->reportable(fn (QueryException $e): bool => ! self::isReadOnlyError($e));

        $this->renderable(function (QueryException $e): \Illuminate\Http\Response|bool {
            if (self::isReadOnlyError($e)) {
                return response()->view('errors.503', [], 503);
            }

            return false;
        });
    }

    private static function isReadOnlyError(QueryException $e): bool
    {
        return 1 === preg_match(
            '/SQLSTATE\[HY000\]: General error: 1290 The (MySQL|MariaDB) server is running with the --read-only(=ON)? option so it cannot execute this statement/',
            $e->getMessage(),
        );
    }
}

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

namespace App\Providers;

use App\Listeners\AchievementUnlocked;
use App\Listeners\LoginListener;
use App\Listeners\RegisteredListener;
use Assada\Achievements\Event\Unlocked;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Override;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        // Login Timestamp
        Login::class => [
            LoginListener::class,
        ],

        Registered::class => [
            RegisteredListener::class,
        ],

        // Achievements System
        Unlocked::class => [
            AchievementUnlocked::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    #[Override]
    public function boot(): void
    {
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    #[Override]
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

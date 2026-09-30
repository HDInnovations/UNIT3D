<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\User;
use App\Notifications\NewWelcome;
use App\Repositories\ChatRepository;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Arr;

readonly class RegisteredListener
{
    public function __construct(private ChatRepository $chatRepository)
    {
    }

    public function __invoke(Registered $event): void
    {
        /** @var User $user */
        $user = $event->user;

        $this->chatRepository->systemMessage($this->getWelcomeMessage($user));

        // Send Welcome PM
        $user->notify(new NewWelcome());
    }

    private function getWelcomeMessage(User $user): string
    {
        // Select A Random Welcome Message
        $profileUrl = href_profile($user);

        $locale = config('app.locale');
        $replace = ['url' => $profileUrl, 'username' => $user->username, 'site' => config('other.title')];

        return Arr::random([
            trans('application-messages.bot.welcome-1', $replace, $locale),
            trans('application-messages.bot.welcome-2', $replace, $locale),
            trans('application-messages.bot.welcome-3', $replace, $locale),
            trans('application-messages.bot.welcome-4', $replace, $locale),
            trans('application-messages.bot.welcome-5', $replace, $locale),
            trans('application-messages.bot.welcome-6', $replace, $locale),
            trans('application-messages.bot.welcome-7', $replace, $locale),
        ]);
    }
}

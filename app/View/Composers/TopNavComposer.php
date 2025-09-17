<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\Enums\ModerationStatus;
use App\Models\Donation;
use App\Models\Event;
use App\Models\Page;
use App\Models\Report;
use App\Models\Scopes\ApprovedScope;
use App\Models\Ticket;
use App\Models\Torrent;
use Illuminate\View\View;

class TopNavComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $user = auth()->user()->load('group');

        $view->with([
            'pages' => cache()->flexible(
                'cached-pages',
                [3600, 3600 * 2],
                fn () => Page::select(['id', 'name', 'created_at'])->take(6)->get()
            ),
            'hasUnreadTicket' => Ticket::query()
                ->when(
                    $user->group->is_modo,
                    fn ($query) => $query
                        ->whereNull('closed_at')
                        ->whereNull('staff_id')
                        ->orWhere(
                            fn ($query) => $query
                                ->where('staff_id', '=', $user->id)
                                ->where('staff_read', '=', false)
                        ),
                    fn ($query) => $query
                        ->where('user_id', '=', $user->id)
                        ->where('user_read', '=', false),
                )
                ->exists(),
            'events' => Event::query()
                ->where('active', '=', true)
                ->withExists([
                    'claimedPrizes' => fn ($query) => $query
                        ->where('created_at', '>', now()->startOfDay())
                        ->where('user_id', '=', $user->id),
                ])
                ->get(),
            'donationPercentage' => value(function (): int|string {
                $sum = Donation::query()
                    ->join('donation_packages', 'donations.package_id', '=', 'donation_packages.id')
                    ->where('donations.created_at', '>=', now()->startOfMonth())
                    ->where('donations.status', ModerationStatus::APPROVED)
                    ->sum('donation_packages.cost');

                return $sum ? min(100, number_format(($sum / config('donation.monthly_goal')) * 100)) : 0;
            }),
            // Generally sites have more seeders than leechers, so it ends up being faster (by approximately 50%) to compute these stats instead of computing them individually
            'peerCount' => cache()->flexible(
                "users:{$user->id}:peer-count",
                [60, 60 * 2],
                fn () => $user->peers()->where('active', '=', 1)->count(),
            ),
            'leechCount' => cache()->flexible(
                "users:{$user->id}:leech-count",
                [60, 60 * 2],
                fn () => $user->peers()->where('active', '=', 1)->where('seeder', '=', false)->count(),
            ),
            'hasActiveWarning'    => $user->warnings()->where('active', '=', true)->exists(),
            'hasUnresolvedReport' => $user->group->is_modo && Report::query()->whereNull('snoozed_until')->where(
                'solved',
                '=',
                false
            )->exists(),
            'hasUnmoderatedTorrent' => $user->group->is_torrent_modo && Torrent::query()
                ->withoutGlobalScope(ApprovedScope::class)
                ->where('status', '=', ModerationStatus::PENDING)
                ->exists(),
            'hasUnreadPm'           => $user->participations()->where('read', '=', false)->exists(),
            'hasUnreadNotification' => $user->unreadNotifications()->exists(),
            'uploadCount'           => cache()->flexible(
                "users:{$user->id}:upload-count",
                [60, 60 * 2],
                fn () => $user->torrents()->count(),
            ),
            'downloadCount' => cache()->flexible(
                "users:{$user->id}:download-count",
                [60, 60 * 2],
                fn () => $user->history()->withoutGlobalScopes()->where('actual_downloaded', '>', 0)->count(),
            ),
            'user' => $user,
        ]);
    }
}

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

namespace App\Http\Controllers\Staff;

use App\Enums\ModerationStatus;
use App\Helpers\TorrentHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\UpdateModerationRequest;
use App\Models\Conversation;
use App\Models\PrivateMessage;
use App\Models\Scopes\ApprovedScope;
use App\Models\Torrent;
use App\Repositories\ChatRepository;
use App\Services\Unit3dAnnounce;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\Staff\ModerationControllerTest
 */
class ModerationController extends Controller
{
    /**
     * ModerationController Constructor.
     */
    public function __construct(private readonly ChatRepository $chatRepository)
    {
    }

    /**
     * Torrent Moderation Panel.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        abort_unless(auth()->user()->group->is_torrent_modo, 403);

        return view('Staff.moderation.index', [
            'current' => now(),
            'pending' => Torrent::withoutGlobalScope(ApprovedScope::class)
                ->with(['user.group', 'category', 'type', 'resolution'])
                ->where('status', '=', ModerationStatus::PENDING)
                ->get(),
            'postponed' => Torrent::withoutGlobalScope(ApprovedScope::class)
                ->with(['user.group', 'moderated.group', 'category', 'type', 'resolution'])
                ->where('status', '=', ModerationStatus::POSTPONED)
                ->get(),
            'rejected' => Torrent::withoutGlobalScope(ApprovedScope::class)
                ->with(['user.group', 'moderated.group', 'category', 'type', 'resolution'])
                ->where('status', '=', ModerationStatus::REJECTED)
                ->get(),
        ]);
    }

    /**
     * Update a torrent's moderation status.
     */
    public function update(UpdateModerationRequest $request, int $id): \Illuminate\Http\RedirectResponse
    {
        abort_unless(auth()->user()->group->is_torrent_modo, 403);

        $torrent = Torrent::withoutGlobalScope(ApprovedScope::class)->with('user')->findOrFail($id);

        if (ModerationStatus::from($request->integer('old_status')) !== $torrent->status) {
            return to_route('torrents.show', ['id' => $id])
                ->withInput()
                ->withErrors('Torrent has already been moderated since this page was loaded.');
        }

        if (ModerationStatus::from($request->integer('status')) === $torrent->status) {
            return to_route('torrents.show', ['id' => $id])
                ->withInput()
                ->withErrors(
                    match ($torrent->status) {
                        ModerationStatus::PENDING   => 'Torrent already pending.',
                        ModerationStatus::APPROVED  => 'Torrent already approved.',
                        ModerationStatus::REJECTED  => 'Torrent already rejected.',
                        ModerationStatus::POSTPONED => 'Torrent already postponed.',
                    }
                );
        }

        $staff = auth()->user();

        switch (ModerationStatus::from($request->integer('status'))) {
            case ModerationStatus::APPROVED:
                // Announce To Shoutbox
                if (!$torrent->anon) {
                    $this->chatRepository->systemMessage(
                        trans('application-messages.bot.torrent-uploaded', [
                            'userUrl'  => \sprintf('%s/users/%s', config('app.url'), $torrent->user->username),
                            'username' => $torrent->user->username,
                            'category' => $torrent->category->name,
                            'url'      => \sprintf('%s/torrents/%s', config('app.url'), $id),
                            'name'     => $torrent->name,
                        ], config('app.locale'))
                    );
                } else {
                    $this->chatRepository->systemMessage(
                        trans('application-messages.bot.torrent-uploaded-anon', [
                            'category' => $torrent->category->name,
                            'url'      => \sprintf('%s/torrents/%s', config('app.url'), $id),
                            'name'     => $torrent->name,
                        ], config('app.locale'))
                    );
                }

                TorrentHelper::approveHelper($id);

                return to_route('staff.moderation.index')
                    ->with('success', __('application-messages.flash.torrent-approved'));

            case ModerationStatus::REJECTED:
                $torrent->update([
                    'status'       => ModerationStatus::REJECTED,
                    'moderated_at' => now(),
                    'moderated_by' => $staff->id,
                ]);

                $rejectRecipientLocale = $torrent->user->preferredLocale();

                $conversation = Conversation::create(['subject' => trans('application-messages.mail.torrent-rejected-subject', [
                    'name'     => $torrent->name,
                    'username' => $staff->username,
                ], $rejectRecipientLocale)]);

                $conversation->users()->sync([$staff->id => ['read' => true], $torrent->user_id]);

                PrivateMessage::create([
                    'conversation_id' => $conversation->id,
                    'sender_id'       => $staff->id,
                    'message'         => trans('application-messages.mail.torrent-rejected-message', [
                        'id'       => $id,
                        'name'     => $torrent->name,
                        'username' => $staff->username,
                        'note'     => $request->message,
                    ], $rejectRecipientLocale),
                ]);

                cache()->forget('announce-torrents:by-infohash:'.$torrent->info_hash);

                Unit3dAnnounce::addTorrent($torrent);

                return to_route('staff.moderation.index')
                    ->with('success', __('application-messages.flash.torrent-rejected'));

            case ModerationStatus::POSTPONED:
                $torrent->update([
                    'status'       => ModerationStatus::POSTPONED,
                    'moderated_at' => now(),
                    'moderated_by' => $staff->id,
                ]);

                $postponeRecipientLocale = $torrent->user->preferredLocale();

                $conversation = Conversation::create(['subject' => trans('application-messages.mail.torrent-postponed-subject', [
                    'name'     => $torrent->name,
                    'username' => $staff->username,
                ], $postponeRecipientLocale)]);

                $conversation->users()->sync([$staff->id => ['read' => true], $torrent->user_id]);

                PrivateMessage::create([
                    'conversation_id' => $conversation->id,
                    'sender_id'       => $staff->id,
                    'message'         => trans('application-messages.mail.torrent-postponed-message', [
                        'id'       => $id,
                        'name'     => $torrent->name,
                        'username' => $staff->username,
                        'note'     => $request->message,
                    ], $postponeRecipientLocale),
                ]);

                cache()->forget('announce-torrents:by-infohash:'.$torrent->info_hash);

                Unit3dAnnounce::addTorrent($torrent);

                return to_route('staff.moderation.index')
                    ->with('success', __('application-messages.flash.torrent-postponed'));

            default: // Undefined status
                return to_route('torrents.show', ['id' => $id])
                    ->withErrors('Invalid moderation status.');
        }
    }
}

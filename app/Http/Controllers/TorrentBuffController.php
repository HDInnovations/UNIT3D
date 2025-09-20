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

namespace App\Http\Controllers;

use App\Bots\IRCAnnounceBot;
use App\Models\FeaturedTorrent;
use App\Models\FreeleechToken;
use App\Models\Scopes\ApprovedScope;
use App\Models\Torrent;
use App\Repositories\ChatRepository;
use App\Services\Unit3dAnnounce;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\TorrentControllerTest
 */
class TorrentBuffController extends Controller
{
    /**
     * TorrentController Constructor.
     */
    public function __construct(private readonly ChatRepository $chatRepository)
    {
    }

    /**
     * Bump A Torrent.
     */
    public function bumpTorrent(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->group->is_modo || $user->internals()->exists(), 403);
        $torrent = Torrent::withoutGlobalScope(ApprovedScope::class)->findOrFail($id);
        $torrent->bumped_at = Carbon::now();
        $torrent->save();

        // Announce To Chat
        $torrentUrl = href_torrent($torrent);
        $profileUrl = href_profile($user);

        $this->chatRepository->systemMessage(
            \sprintf('Attention, [url=%s]%s[/url] has been bumped to the top by [url=%s]%s[/url]! It could use more seeds!', $torrentUrl, $torrent->name, $profileUrl, $user->username)
        );

        // Announce To IRC
        if (config('irc-bot.enabled')) {
            (new IRCAnnounceBot())
                ->to(config('irc-bot.channel'))
                ->say('['.config('app.name').'] User '.$user->username.' has bumped '.$torrent->name.' , it could use more seeds!')
                ->say('[Category: '.$torrent->category->name.'] [Type: '.$torrent->type->name.'] [Size:'.$torrent->getSize().']')
                ->say(\sprintf('[Link: %s]', $torrentUrl));
        }

        return to_route('torrents.show', ['id' => $torrent->id])
            ->with('success', 'Torrent has been bumped to the top successfully!');
    }

    /**
     * Sticky A Torrent.
     */
    public function sticky(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->group->is_modo || $user->internals()->exists(), 403);
        $torrent = Torrent::withoutGlobalScope(ApprovedScope::class)->findOrFail($id);
        $torrent->sticky = !$torrent->sticky;
        $torrent->save();

        return to_route('torrents.show', ['id' => $torrent->id])
            ->with('success', 'Torrent sticky status has been adjusted!');
    }

    /**
     * Freeleech A Torrent (1% to 100% Free).
     */
    public function grantFL(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->group->is_modo || $user->internals()->exists(), 403);
        $torrent = Torrent::withoutGlobalScope(ApprovedScope::class)->findOrFail($id);
        $torrentUrl = href_torrent($torrent);

        $request->validate([
            'freeleech' => 'numeric|min:0|max:100',
            'fl_until'  => 'nullable|numeric'
        ]);

        if ($request->freeleech != 0) {
            if ($request->fl_until !== null) {
                $torrent->fl_until = Carbon::now()->addDays($request->integer('fl_until'));
                $this->chatRepository->systemMessage(
                    \sprintf('Ladies and Gents, [url=%s]%s[/url] has been granted %s%% FreeLeech for '.$request->fl_until.' days.', $torrentUrl, $torrent->name, $request->freeleech)
                );
            } else {
                $this->chatRepository->systemMessage(
                    \sprintf('Ladies and Gents, [url=%s]%s[/url] has been granted %s%% FreeLeech! Grab It While You Can!', $torrentUrl, $torrent->name, $request->freeleech)
                );
            }
        } elseif ($torrent->free != 0) {
            $this->chatRepository->systemMessage(
                \sprintf('Ladies and Gents, [url=%s]%s[/url] has been revoked of its %s%% FreeLeech!', $torrentUrl, $torrent->name, $torrent->free)
            );
        }

        $torrent->free = $request->freeleech;
        $torrent->save();

        cache()->forget('announce-torrents:by-infohash:'.$torrent->info_hash);

        Unit3dAnnounce::addTorrent($torrent);

        return to_route('torrents.show', ['id' => $torrent->id])
            ->with('success', 'Torrent FL has been adjusted!');
    }

    /**
     * Feature A Torrent.
     */
    public function grantFeatured(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->group->is_modo || $user->internals()->exists(), 403);
        $torrent = Torrent::withoutGlobalScope(ApprovedScope::class)->findOrFail($id);

        if ($torrent->featured()->doesntExist()) {
            Unit3dAnnounce::addFeaturedTorrent($torrent->id);

            $featured = new FeaturedTorrent();
            $featured->user_id = $user->id;
            $featured->torrent_id = $torrent->id;
            $featured->save();

            cache()->forget('featured-torrent-ids');

            $torrentUrl = href_torrent($torrent);
            $profileUrl = href_profile($user);
            $this->chatRepository->systemMessage(
                \sprintf('Ladies and Gents, [url=%s]%s[/url] has been added to the Featured Torrents Slider by [url=%s]%s[/url]! Grab It While You Can!', $torrentUrl, $torrent->name, $profileUrl, $user->username)
            );

            return to_route('torrents.show', ['id' => $torrent->id])
                ->with('success', 'Torrent is now featured!');
        }

        return to_route('torrents.show', ['id' => $torrent->id])
            ->withErrors('Torrent is already featured!');
    }

    /**
     * UnFeature A Torrent.
     */
    public function revokeFeatured(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->group->is_modo, 403);

        $featured_torrent = FeaturedTorrent::where('torrent_id', '=', $id)->sole();

        $torrent = Torrent::withoutGlobalScope(ApprovedScope::class)->findOrFail($id);

        Unit3dAnnounce::removeFeaturedTorrent($torrent->id);

        $appurl = config('app.url');

        $this->chatRepository->systemMessage(
            \sprintf('Ladies and Gents, [url=%s/torrents/%s]%s[/url] is no longer featured.', $appurl, $torrent->id, $torrent->name)
        );

        $featured_torrent->delete();

        cache()->forget('featured-torrent-ids');

        return to_route('torrents.show', ['id' => $torrent->id])
            ->with('success', 'Revoked featured from Torrent!');
    }

    /**
     * Double Upload A Torrent.
     */
    public function grantDoubleUp(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->group->is_modo || $user->internals()->exists(), 403);
        $torrent = Torrent::withoutGlobalScope(ApprovedScope::class)->findOrFail($id);
        $torrentUrl = href_torrent($torrent);

        if (!$torrent->doubleup) {
            $torrent->doubleup = true;
            $du_until = $request->input('du_until');

            if ($du_until !== null) {
                $torrent->du_until = Carbon::now()->addDays($request->integer('du_until'));
                $this->chatRepository->systemMessage(
                    \sprintf('Ladies and Gents, [url=%s]%s[/url] has been granted Double Upload for '.$request->input('du_until').' days.', $torrentUrl, $torrent->name)
                );
            } else {
                $this->chatRepository->systemMessage(
                    \sprintf('Ladies and Gents, [url=%s]%s[/url] has been granted Double Upload! Grab It While You Can!', $torrentUrl, $torrent->name)
                );
            }
        } else {
            $torrent->doubleup = false;
            $this->chatRepository->systemMessage(
                \sprintf('Ladies and Gents, [url=%s]%s[/url] has been revoked of its Double Upload!', $torrentUrl, $torrent->name)
            );
        }

        $torrent->save();

        cache()->forget('announce-torrents:by-infohash:'.$torrent->info_hash);

        Unit3dAnnounce::addTorrent($torrent);

        return to_route('torrents.show', ['id' => $torrent->id])
            ->with('success', 'Torrent DoubleUpload has been adjusted!');
    }

    /**
     * Use Freeleech Token On A Torrent.
     */
    public function freeleechToken(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
        $torrent = Torrent::withoutGlobalScope(ApprovedScope::class)->findOrFail($id);

        $activeToken = cache()->get('freeleech_token:'.$user->id.':'.$torrent->id);

        if ($user->fl_tokens >= 1 && !$activeToken) {
            $freeleechToken = new FreeleechToken();
            $freeleechToken->user_id = $user->id;
            $freeleechToken->torrent_id = $torrent->id;
            $freeleechToken->save();

            Unit3dAnnounce::addFreeleechToken($user->id, $torrent->id);

            $user->fl_tokens -= 1;
            $user->save();

            cache()->put('freeleech_token:'.$user->id.':'.$torrent->id, true);

            $torrent->searchable();

            return to_route('torrents.show', ['id' => $torrent->id])
                ->with('success', 'You have successfully activated a freeleech token for this torrent!');
        }

        return to_route('torrents.show', ['id' => $torrent->id])
            ->withErrors('You don\'t have enough freeleech tokens or already have one activated on this torrent.');
    }

    /**
     * Set Torrents Refundable Status.
     */
    public function setRefundable(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->group->is_modo || $user->internals()->exists(), 403);

        $torrent = Torrent::withoutGlobalScope(ApprovedScope::class)->findOrFail($id);
        $torrent_url = href_torrent($torrent);

        if (!$torrent->refundable) {
            $torrent->refundable = true;

            $this->chatRepository->systemMessage(
                \sprintf('Ladies and Gents, [url=%s]%s[/url] is now refundable! Grab It While You Can!', $torrent_url, $torrent->name)
            );
        } else {
            $torrent->refundable = false;

            $this->chatRepository->systemMessage(
                \sprintf('Ladies and Gents, [url=%s]%s[/url] is no longer refundable!', $torrent_url, $torrent->name)
            );
        }

        $torrent->save();

        return to_route('torrents.show', ['id' => $torrent->id])
            ->with('success', 'Torrent\'s Refundable Status Has Been Adjusted!');
    }
}

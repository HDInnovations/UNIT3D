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

use App\Achievements\UserUploaded1000Subtitles;
use App\Achievements\UserUploaded100Subtitles;
use App\Achievements\UserUploaded200Subtitles;
use App\Achievements\UserUploaded25Subtitles;
use App\Achievements\UserUploaded300Subtitles;
use App\Achievements\UserUploaded400Subtitles;
use App\Achievements\UserUploaded500Subtitles;
use App\Achievements\UserUploaded50Subtitles;
use App\Achievements\UserUploaded600Subtitles;
use App\Achievements\UserUploaded700Subtitles;
use App\Achievements\UserUploaded800Subtitles;
use App\Achievements\UserUploaded900Subtitles;
use App\Achievements\UserUploadedFirstSubtitle;
use App\Enums\ModerationStatus;
use App\Http\Requests\StoreSubtitleRequest;
use App\Http\Requests\UpdateSubtitleRequest;
use App\Models\Chatroom;
use App\Models\MediaLanguage;
use App\Models\Scopes\ApprovedScope;
use App\Models\Subtitle;
use App\Models\Torrent;
use App\Models\User;
use App\Notifications\Chatbox\SubtitleCreate;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;

class SubtitleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('subtitle.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('subtitle.create', [
            'torrent'         => Torrent::query()->withoutGlobalScope(ApprovedScope::class)->findOrFail($request->integer('torrent_id')),
            'media_languages' => MediaLanguage::query()->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubtitleRequest $request, #[CurrentUser] User $user): \Illuminate\Http\RedirectResponse
    {
        $filename = uniqid('', true).'.'.$request->subtitleFile->getClientOriginalExtension();

        $torrent = Torrent::query()->withoutGlobalScope(ApprovedScope::class)->findOrFail($request->torrentId);

        $subtitle = Subtitle::query()->create([
            ...$request->toArray(),
            'title'        => $torrent->name,
            'file_name'    => $filename,
            'file_size'    => $request->subtitleFile->getSize(),
            'extension'    => '.'.$request->subtitleFile->getClientOriginalExtension(),
            'downloads'    => 0,
            'verified'     => 0,
            'user_id'      => $user->id,
            'status'       => ModerationStatus::APPROVED,
            'moderated_at' => now(),
            'moderated_by' => User::SYSTEM_USER_ID,
        ]);

        // Save Subtitle
        Storage::disk('subtitle-files')->putFileAs('', $request->subtitleFile, $filename);

        // Announce To Shoutbox
        Chatroom::systemChatroom()->notify(new SubtitleCreate($subtitle));

        if (!$subtitle->anon) {
            // Achievements
            $user->unlock(new UserUploadedFirstSubtitle());
            $user->addProgress(new UserUploaded25Subtitles(), 1);
            $user->addProgress(new UserUploaded50Subtitles(), 1);
            $user->addProgress(new UserUploaded100Subtitles(), 1);
            $user->addProgress(new UserUploaded200Subtitles(), 1);
            $user->addProgress(new UserUploaded300Subtitles(), 1);
            $user->addProgress(new UserUploaded400Subtitles(), 1);
            $user->addProgress(new UserUploaded500Subtitles(), 1);
            $user->addProgress(new UserUploaded600Subtitles(), 1);
            $user->addProgress(new UserUploaded700Subtitles(), 1);
            $user->addProgress(new UserUploaded800Subtitles(), 1);
            $user->addProgress(new UserUploaded900Subtitles(), 1);
            $user->addProgress(new UserUploaded1000Subtitles(), 1);
        }

        return to_route('torrents.show', ['id' => $request->torrentId])
            ->with('success', 'Subtitle successfully added');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubtitleRequest $request, Subtitle $subtitle, #[CurrentUser] User $currentUser): \Illuminate\Http\RedirectResponse
    {
        abort_unless($currentUser->group->is_modo || $currentUser->id == $subtitle->user_id, 403);

        $subtitle->update($request->toArray());

        return to_route('torrents.show', ['id' => $subtitle->torrent_id])
            ->with('success', 'Subtitle successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws Exception
     */
    public function destroy(Request $request, Subtitle $subtitle): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->group->is_modo || $user->id === $subtitle->user_id, 403);

        if (Storage::disk('subtitle-files')->exists($subtitle->file_name)) {
            Storage::disk('subtitle-files')->delete($subtitle->file_name);
        }

        $subtitle->delete();

        return to_route('torrents.show', ['id' => $subtitle->torrent_id])
            ->with('success', 'Subtitle successfully deleted');
    }

    /**
     * Download the specified resource from storage.
     */
    public function download(Request $request, Subtitle $subtitle): \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse|\Symfony\Component\HttpFoundation\StreamedResponse
    {
        $user = $request->user();

        // User's download rights are revoked
        if ($user->can_download == 0 && $subtitle->user_id != $user->id) {
            return to_route('torrents.show', ['id' => $subtitle->torrent->id])
                ->withErrors('Your download rights have been revoked!');
        }

        // Define the filename for the download
        $tempFilename = sanitize_filename('['.$subtitle->language->name.' Subtitle]'.$subtitle->torrent->name.$subtitle->extension);

        // Increment downloads count
        $subtitle->increment('downloads');

        $headers = ['Content-Type: '.Storage::disk('subtitle-files')->mimeType($subtitle->file_name)];

        return Storage::disk('subtitle-files')->download($subtitle->file_name, $tempFilename, $headers);
    }
}

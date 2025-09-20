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
use App\Models\MediaLanguage;
use App\Models\Scopes\ApprovedScope;
use App\Models\Subtitle;
use App\Models\Torrent;
use App\Models\User;
use App\Repositories\ChatRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;

class SubtitleController extends Controller
{
    /**
     * SubtitleController Constructor.
     */
    public function __construct(private readonly ChatRepository $chatRepository)
    {
    }

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
            'torrent'         => Torrent::withoutGlobalScope(ApprovedScope::class)->findOrFail($request->integer('torrent_id')),
            'media_languages' => MediaLanguage::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubtitleRequest $request): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
        $subtitleFile = $request->file('subtitle_file');

        abort_if(\is_array($subtitleFile), 400);

        $filename = uniqid('', true).'.'.$subtitleFile->getClientOriginalExtension();

        $torrent = Torrent::withoutGlobalScope(ApprovedScope::class)->findOrFail($request->integer('torrent_id'));

        $subtitle = Subtitle::create([
            'title'        => $torrent->name,
            'file_name'    => $filename,
            'file_size'    => $subtitleFile->getSize(),
            'extension'    => '.'.$subtitleFile->getClientOriginalExtension(),
            'downloads'    => 0,
            'verified'     => 0,
            'user_id'      => $user->id,
            'status'       => ModerationStatus::APPROVED,
            'moderated_at' => now(),
            'moderated_by' => User::SYSTEM_USER_ID,
        ] + $request->safe()->except('subtitle_file'));

        // Save Subtitle
        Storage::disk('subtitle-files')->putFileAs('', $subtitleFile, $filename);

        // Announce To Shoutbox
        if (!$subtitle->anon) {
            $this->chatRepository->systemMessage(
                \sprintf(
                    '[url=%s]%s[/url] has uploaded a new %s subtitle for [url=%s]%s[/url]',
                    href_profile($user),
                    $user->username,
                    $subtitle->language->name,
                    href_torrent($torrent),
                    $subtitle->torrent->name
                )
            );

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
        } else {
            $this->chatRepository->systemMessage(
                \sprintf(
                    'An anonymous user has uploaded a new %s subtitle for [url=%s]%s[/url]',
                    $subtitle->language->name,
                    href_torrent($torrent),
                    $subtitle->torrent->name
                )
            );
        }

        return to_route('torrents.show', ['id' => $request->input('torrent_id')])
            ->with('success', 'Subtitle successfully added');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubtitleRequest $request, Subtitle $subtitle): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->group->is_modo || $request->user()->id == $subtitle->user_id, 403);

        $subtitle->update($request->validated());

        return to_route('torrents.show', ['id' => $request->input('torrent_id')])
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

        return to_route('torrents.show', ['id' => $request->integer('torrent_id')])
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

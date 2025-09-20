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
use App\Helpers\StringHelper;
use App\Models\Conversation;
use App\Services\Unit3dAnnounce;
use App\Models\Donation;
use Illuminate\Http\Request;
use App\Models\PrivateMessage;
use App\Http\Controllers\Controller;

class DonationController extends Controller
{
    /**
     * Get All Donations.
     */
    public function index(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        abort_unless($request->user()->group->is_owner, 403);

        $donations = Donation::with(['package' => function ($query): void {
            $query->withTrashed();
        }])->latest()->paginate(25);

        $dailyDonations = Donation::selectRaw('DATE(donations.created_at) as date, SUM(donation_packages.cost) as total')
            ->join('donation_packages', 'donations.package_id', '=', 'donation_packages.id')
            ->where('donations.status', '=', ModerationStatus::APPROVED)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $monthlyDonations = Donation::selectRaw('EXTRACT(YEAR FROM donations.created_at) as year, EXTRACT(MONTH FROM donations.created_at) as month, SUM(donation_packages.cost) as total')
            ->join('donation_packages', 'donations.package_id', '=', 'donation_packages.id')
            ->where('donations.status', '=', ModerationStatus::APPROVED)
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return view('Staff.donation.index', [
            'donations'        => $donations,
            'dailyDonations'   => $dailyDonations,
            'monthlyDonations' => $monthlyDonations,
        ]);
    }

    /**
     * Update A Donation.
     */
    public function update(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->group->is_owner, 403);

        $now = now();

        $donation = Donation::with(['user', 'package'])->findOrFail($id);
        $donation->status = ModerationStatus::APPROVED;
        $donation->starts_at = $now;

        if ($donation->package->donor_value > 0) {
            $donation->ends_at = $now->addDays($donation->package->donor_value);
        } else {
            $donation->ends_at = null;
        }

        $donation->user->invites += $donation->package->invite_value ?? 0;
        $donation->user->uploaded += $donation->package->upload_value ?? 0;
        $donation->user->is_donor = true;
        $donation->user->is_lifetime = $donation->package->donor_value === null;
        $donation->user->seedbonus += $donation->package->bonus_value ?? 0;
        $donation->user->save();

        $conversation = Conversation::create(['subject' => 'Your donation from '.$donation->created_at.', has been approved by '.$request->user()->username]);
        $conversation->users()->sync([$request->user()->id => ['read' => true], $donation->user_id]);

        PrivateMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $request->user()->id,
            'message'         => '[b]Thank you for supporting '.config('app.name').'[/b]'."\n"
                .'Your donation has been approved and is valid through: '.($donation->ends_at ?? 'Lifetime').' (YYYY-MM-DD)'."\n"
                .'A total of '.number_format(($donation->package->bonus_value ?? 0)).' BON points, '.StringHelper::formatBytes(($donation->package->upload_value ?? 0)).' upload and '.($donation->package->invite_value ?? 0).' invites have been credited to your account.',
        ]);

        $donation->save();

        cache()->forget('user:'.$donation->user->passkey);
        Unit3dAnnounce::addUser($donation->user);

        return redirect()->route('staff.donations.index')
            ->with('success', 'Donation approved!');
    }

    /**
     * Destroy A Donation.
     */
    public function destroy(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->group->is_owner, 403);

        $donation = Donation::findOrFail($id);
        $donation->status = ModerationStatus::REJECTED;

        $conversation = Conversation::create(['subject' => 'Your donation from '.$donation->created_at.', has been rejected by '.$request->user()->username]);
        $conversation->users()->sync([$request->user()->id => ['read' => true], $donation->user_id]);

        PrivateMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $request->user()->id,
            'message'         => 'Your donation could not be approved at this time. Please contact us for more information by replying to this private message.',
        ]);

        $donation->save();

        return redirect()->route('staff.donations.index')
            ->with('success', 'Donation rejected!');
    }
}

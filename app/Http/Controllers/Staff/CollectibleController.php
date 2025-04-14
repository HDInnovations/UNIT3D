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
 * @author     Obi-Wana
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\StoreCollectibleRequest;
use App\Http\Requests\Staff\UpdateCollectibleRequest;
use App\Models\Collectible;
use App\Models\CollectibleCategory;
use App\Models\CollectibleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Intervention\Image\Facades\Image;

class CollectibleController extends Controller
{
    /**
     * Display All Collectibles.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.collectible.index', [
            'collectibleCategories' => CollectibleCategory::orderBy('position')->get(),
            'collectibles'          => Collectible::get(),
        ]);
    }

    /**
     * Show Collectible Create Form.
     */
    public function create(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.collectible.create', [
            'collectibleCategoryId' => $request->integer('collectibleCategoryId'),
            'collectibleCategories' => CollectibleCategory::orderBy('name')->get(),
        ]);
    }

    /**
     * Store A New Collectible Item.
     */
    public function store(StoreCollectibleRequest $request): \Illuminate\Http\RedirectResponse
    {
        // Add the icon
        if ($request->hasFile('collectible.icon')) {
            $icon = $request->file('collectible.icon');

            abort_if(\is_array($icon), 400);

            if (!\in_array($icon->getClientOriginalExtension(), ['jpg', 'JPG', 'jpeg', 'bmp', 'png', 'PNG', 'tiff'])) {
                return to_route('staff.collectibles.index')
                    ->withErrors('Only .jpg, .bmp, .png and .tiff are allowed.');
            }

            $filename = 'collectibles-'.uniqid('', true).'.'.$icon->getClientOriginalExtension();
            $path = Storage::disk('collectible-icons')->path($filename);
            Image::make($icon->getRealPath())->fit(150, 150)->encode('png', 100)->save($path);
        }

        // Create the collectible
        $collectible = Collectible::create(['icon' => $filename ?? null] + $request->validated('collectible'));

        $collectible->requirements()->upsert($request->validated('requirements', []), ['id']);

        // Create the collectible items based on max_amount.
        // The collectible item is not linked to any users, means its
        // available to be bought from the store (not user-user trade).
        $collectibleItemsData = array_map(fn () => [
            'collectible_id' => $collectible->id,
            'created_at'     => now(),
            'updated_at'     => now(),
        ], array_fill(0, (int) $request->input('collectible.max_amount'), null));

        CollectibleItem::insert($collectibleItemsData);

        return to_route('staff.collectibles.index')
            ->with('success', 'Item has been created successfully.');
    }

    /**
     * Collectible Edit Form.
     */
    public function edit(Collectible $collectible): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.collectible.edit', [
            'collectible'           => $collectible,
            'requirements'          => $collectible->requirements,
            'collectibleCategories' => CollectibleCategory::orderBy('name')->get(),
            'max_amount'            => $collectible->items()->count(),
        ]);
    }

    /**
     * Edit A Collectible.
     */
    public function update(UpdateCollectibleRequest $request, Collectible $collectible): \Illuminate\Http\RedirectResponse
    {
        // Update the collectible items based on max_amount, which is the diff between
        // the existing CollectibleItems and the new max_amount.
        $diff = $request->collectible['max_amount'] - $collectible->items->count();
        $unassignedItems = $collectible->items()->whereNull('user_id')->get();

        // More items to be added
        $collectibleItemsData = [];

        if ($diff > 0) {
            $collectibleItemsData = array_map(fn () => [
                'collectible_id' => $collectible->id,
                'created_at'     => now(),
                'updated_at'     => now(),
            ], array_fill(0, $diff, null));

            CollectibleItem::insert($collectibleItemsData);
        }
        // Items to be deleted
        elseif ($diff < 0 && $unassignedItems->count() >= ($collectible->items->count() - $request->collectible['max_amount'])) {
            CollectibleItem::where('collectible_id', $collectible->id)
                ->whereNull('user_id')
                ->limit(abs($diff))
                ->delete();
        }
        // Can not delete any items due to being owned by users
        elseif (abs($diff) > $unassignedItems->count()) {
            return to_route('staff.collectibles.index')
                ->withErrors('Unable to decrease the maximum available amount for this item because users already own that many. Please try reducing the amount by a smaller value.');
        }

        // Only update the Icon if a new was uploaded
        if ($request->hasFile('icon')) {
            $icon = $request->file('icon');

            abort_if(\is_array($icon), 400);

            $filename = 'collectibles-'.uniqid('', true).'.'.$icon->getClientOriginalExtension();
            $path = Storage::disk('collectible-icons')->path($filename);
            Image::make($icon->getRealPath())->fit(150, 150)->encode('png', 100)->save($path);

            $collectible->update(['icon' => $filename] + $request->validated('collectible'));
        } else {
            $oldIcon = $collectible->icon;
            $collectible->update(['icon' => $oldIcon ?? null] + $request->validated('collectible'));
        }

        // Update conditions
        $collectible->requirements()
            ->whereNotIn('id', Arr::flatten($request->validated('requirements.id', [])))
            ->delete();
        $collectible->requirements()->upsert($request->validated('requirements', []), ['id']);

        return to_route('staff.collectibles.index')
            ->with('success', 'Item has been updated successfully.');
    }

    /**
     * Delete A Collectible.
     */
    public function destroy(Collectible $collectible): \Illuminate\Http\RedirectResponse
    {
        // Finally, delete the collectible itself
        // including all related entries (items, transactions, offers, requirements)
        $collectible->delete();

        // Delete the icon file from storage
        if ($collectible->icon !== null) {
            Storage::disk('collectible-icons')->delete($collectible->icon);
        }

        return to_route('staff.collectibles.index')
            ->with('success', 'Item has been deleted successfully.');
    }
}

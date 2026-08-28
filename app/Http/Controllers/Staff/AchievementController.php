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
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Controllers\Staff;

use App\Enums\AchievementConditionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\StoreAchievementRequest;
use App\Http\Requests\Staff\UpdateAchievementRequest;
use App\Models\Achievement;
use App\Models\Category;
use App\Models\Playlist;
use App\Models\Resolution;
use App\Models\Type;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class AchievementController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.achievement.index', [
            'achievements' => Achievement::query()->with('tiers')->orderBy('position')->get(),
        ]);
    }

    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.achievement.create', [
            'conditionTypes' => AchievementConditionType::cases(),
            'types'          => Type::query()->orderBy('name')->get(),
            'categories'     => Category::query()->orderBy('name')->get(),
            'resolutions'    => Resolution::query()->orderBy('name')->get(),
            'playlists'      => Playlist::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreAchievementRequest $request): \Illuminate\Http\RedirectResponse
    {
        return DB::transaction(function () use ($request): \Illuminate\Http\RedirectResponse {
            $data = $request->validated('achievement');

            if ($request->hasFile('achievement.icon')) {
                $icon = $request->file('achievement.icon');

                abort_if(\is_array($icon), 400);

                $data['icon_path'] = $this->storeIcon($icon);
            }

            unset($data['icon']);

            $achievement = Achievement::query()->create($data);

            foreach ($request->validated('tiers', []) as $index => $tierData) {
                $tierAttributes = [
                    'achievement_id' => $achievement->id,
                    'tier'           => $index + 1,
                    'name'           => $tierData['name'],
                    'description'    => $tierData['description'],
                    'threshold'      => $tierData['threshold'],
                ];

                if (isset($tierData['icon']) && $request->hasFile("tiers.{$index}.icon")) {
                    $tierIcon = $request->file("tiers.{$index}.icon");

                    abort_if(\is_array($tierIcon), 400);

                    $tierAttributes['icon_path'] = $this->storeIcon($tierIcon);
                }

                $achievement->tiers()->create($tierAttributes);
            }

            return to_route('staff.achievements.index')
                ->with('success', 'Achievement successfully created.');
        });
    }

    public function edit(Achievement $achievement): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.achievement.edit', [
            'achievement'    => $achievement->load('tiers'),
            'conditionTypes' => AchievementConditionType::cases(),
            'types'          => Type::query()->orderBy('name')->get(),
            'categories'     => Category::query()->orderBy('name')->get(),
            'resolutions'    => Resolution::query()->orderBy('name')->get(),
            'playlists'      => Playlist::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateAchievementRequest $request, Achievement $achievement): \Illuminate\Http\RedirectResponse
    {
        return DB::transaction(function () use ($request, $achievement): \Illuminate\Http\RedirectResponse {
            $data = $request->validated('achievement');

            if ($request->hasFile('achievement.icon')) {
                $icon = $request->file('achievement.icon');

                abort_if(\is_array($icon), 400);

                $oldIconPath = $achievement->icon_path;

                $data['icon_path'] = $this->storeIcon($icon);
            }

            unset($data['icon']);

            $achievement->update($data);

            $submittedTierIds = Arr::flatten($request->validated('tiers.*.id', []));

            $achievement->tiers()
                ->whereNotIn('id', array_filter($submittedTierIds))
                ->each(function ($tier): void {
                    if ($tier->icon_path !== null) {
                        Storage::disk('achievement-images')->delete($tier->icon_path);
                    }

                    $tier->delete();
                });

            $achievement->tiers()
                ->whereIn('id', array_filter($submittedTierIds))
                ->increment('tier', 32768);

            foreach ($request->validated('tiers', []) as $index => $tierData) {
                $tierAttributes = [
                    'achievement_id' => $achievement->id,
                    'tier'           => $index + 1,
                    'name'           => $tierData['name'],
                    'description'    => $tierData['description'],
                    'threshold'      => $tierData['threshold'],
                ];

                if (isset($tierData['icon']) && $request->hasFile("tiers.{$index}.icon")) {
                    $tierIcon = $request->file("tiers.{$index}.icon");

                    abort_if(\is_array($tierIcon), 400);

                    $existingTier = !empty($tierData['id'])
                        ? $achievement->tiers()->where('id', '=', $tierData['id'])->first()
                        : null;

                    if ($existingTier?->icon_path !== null) {
                        Storage::disk('achievement-images')->delete($existingTier->icon_path);
                    }

                    $tierAttributes['icon_path'] = $this->storeIcon($tierIcon);
                }

                if (!empty($tierData['id'])) {
                    $achievement->tiers()->where('id', '=', $tierData['id'])->update($tierAttributes);
                } else {
                    $achievement->tiers()->create($tierAttributes);
                }
            }

            if (isset($oldIconPath)) {
                Storage::disk('achievement-images')->delete($oldIconPath);
            }

            return to_route('staff.achievements.index')
                ->with('success', 'Achievement successfully updated.');
        });
    }

    /**
     * @throws Exception
     */
    public function destroy(Achievement $achievement): \Illuminate\Http\RedirectResponse
    {
        if ($achievement->icon_path !== null) {
            Storage::disk('achievement-images')->delete($achievement->icon_path);
        }

        foreach ($achievement->tiers as $tier) {
            if ($tier->icon_path !== null) {
                Storage::disk('achievement-images')->delete($tier->icon_path);
            }
        }

        $achievement->delete();

        return to_route('staff.achievements.index')
            ->with('success', 'Achievement successfully deleted.');
    }

    private function storeIcon(\Illuminate\Http\UploadedFile $file): string
    {
        $filename = 'achievement-'.uniqid('', true).'.jpg';
        $path = Storage::disk('achievement-images')->path($filename);
        Image::make($file->getRealPath())->fit(100, 100)->encode('jpg', 100)->save($path);

        return $filename;
    }
}

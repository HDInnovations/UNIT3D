<?php
namespace App\Http\Controllers\MediaHub;

use App\Http\Controllers\Controller;
use App\Models\MediaHub\StashDBScene;

class PornSceneController extends Controller
{
    /**
     * Display all scenes/torrents for a given studio StashDB ID.
     */
    public function studioResults($id)
    {
        // Find scenes by studio id
        $scenes = \App\Models\MediaHub\StashDBScene::where('studio_id', $id)->get();
        // Find torrents by studio id (if model has studio_id)
        $torrents = \App\Models\Torrent::where('studio_id', $id)->get();
        return view('mediahub.porn.studio_results', compact('id', 'scenes', 'torrents'));
    }

    /**
     * Display all scenes/torrents for a given performer StashDB ID.
     */
    public function performerResults($id)
    {
        // Find scenes by pornstar id (using pornstars relationship)
        $scenes = \App\Models\MediaHub\StashDBScene::whereHas('pornstars', function($q) use ($id) {
            $q->where('pornstar_id', $id);
        })->get();
        // Find torrents by performer id (if model has performer_id)
        $torrents = \App\Models\Torrent::where('performer_id', $id)->get();
        return view('mediahub.porn.performer_results', compact('id', 'scenes', 'torrents'));
    }
    /**
     * Display a list of known stashids.
     */
    public function stashidList()
    {
        $stashids = \DB::table('pornstar_torrent')->select('stashid')->distinct()->pluck('stashid');
        return view('mediahub.porn.stashid_list', compact('stashids'));
    }

    /**
     * Display all torrents for a given stashid.
     */
public function torrentsByStashId($stashid)
{
    $torrents = \App\Models\Torrent::where('stashdb_id', $stashid)->get();
    return view('mediahub.porn.torrents_by_stashid', compact('torrents', 'stashid'));
}
/**
 * Display a grid of scenes by StashDB ID.
 */
public function gridByStashId(string $stashid)
    {
        $scenes = StashDBScene::where('id', $stashid)->get();
        return view('mediahub.porn.grid', compact('scenes', 'stashid'));
    }
    /**
     * Display a listing of porn scenes.
     */
    public function index()
    {
        $scenes = StashDBScene::orderByDesc('release_date')->paginate(20);
        return view('mediahub.porn.index', compact('scenes'));
    }

    /**
     * Display a specific porn scene.
     */
    public function show(string $id)
    {
        $scene = StashDBScene::findOrFail($id);
        return view('mediahub.porn.show', compact('scene'));
    }

    /**
     * Create a StashDBScene entry from a StashDB ID.
     */
    public function storeByStashId(\Illuminate\Http\Request $request)
    {
        $request->validate(['stashdb_id' => 'required|string']);
        $stashdbId = $request->input('stashdb_id');

        // Fetch scene data from StashDB
        $sceneData = app(\App\Services\StashDB\StashDBScraper::class)->scene($stashdbId);
        if (!$sceneData) {
            return back()->withErrors(['stashdb_id' => 'StashDB scene not found.']);
        }

        // Create or update scene entry
        $scene = StashDBScene::updateOrCreate(
            ['id' => $sceneData['id']],
            [
                'studio' => $sceneData['studio']['name'] ?? null,
                'title' => $sceneData['title'] ?? null,
                'urls' => $sceneData['urls'] ?? [],
                'tags' => $sceneData['tags'] ?? [],
                'release_date' => $sceneData['release_date'] ?? null,
                'performers' => $sceneData['performers'] ?? [],
                'fingerprints' => $sceneData['fingerprints'] ?? [],
                'duration' => $sceneData['duration'] ?? null,
                'details' => $sceneData['details'] ?? null,
                'images' => $sceneData['images'] ?? [],
            ]
        );

        return redirect()->route('mediahub.porn.show', $scene->id)
            ->with('success', 'Scene imported from StashDB.');
    }
}

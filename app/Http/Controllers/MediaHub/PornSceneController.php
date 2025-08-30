<?php

namespace App\Http\Controllers\MediaHub;

use App\Http\Controllers\Controller;
use App\Models\MediaHub\StashDBScene;

class PornSceneController extends Controller
{
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
    public function show($id)
    {
        $scene = StashDBScene::findOrFail($id);
        return view('mediahub.porn.show', compact('scene'));
    }
}

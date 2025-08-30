<?php
namespace App\Http\Controllers\MediaHub;

use App\Models\MediaHub\PornStar;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PornStarController extends Controller
{
    public function index()
    {
        $pornstars = PornStar::orderByDesc('scene_count')->paginate(30);
        return view('mediahub.pornstars.index', compact('pornstars'));
    }

    public function show($id)
    {
        $pornstar = PornStar::findOrFail($id);
        return view('mediahub.pornstars.show', compact('pornstar'));
    }
}

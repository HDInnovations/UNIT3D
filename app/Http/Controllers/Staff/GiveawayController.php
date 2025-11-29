<?php

declare(strict_types=1);

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\StoreGiveawayRequest;
use App\Http\Requests\Staff\UpdateGiveawayRequest;
use App\Models\Giveaway;

class GiveawayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.giveaway.index', [
            'giveaways' => Giveaway::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.giveaway.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGiveawayRequest $request): \Illuminate\Http\RedirectResponse
    {
        Giveaway::create($request->validated());

        return to_route('staff.giveaways.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Giveaway $giveaway): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.giveaway.edit', [
            'giveaway' => $giveaway->load('prizes'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGiveawayRequest $request, Giveaway $giveaway): \Illuminate\Http\RedirectResponse
    {
        $giveaway->update($request->validated());

        return to_route('staff.giveaways.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Giveaway $giveaway): \Illuminate\Http\RedirectResponse
    {
        if ($giveaway->claimedPrizes()->exists()) {
            return to_route('staff.giveaways.index')
                ->withErrors('Cannot delete giveaway because users have claimed prizes. You can mark it as inactive instead.');
        }

        $giveaway->delete();

        return to_route('staff.giveaways.index');
    }
}

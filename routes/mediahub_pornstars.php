<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MediaHub\PornStarController;

Route::prefix('mediahub/pornstars')->name('mediahub.pornstars.')->group(function () {
    Route::get('/', [PornStarController::class, 'index'])->name('index');
    Route::get('/{id}', [PornStarController::class, 'show'])->name('show');
});

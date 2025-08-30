<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MediaHub\PornSceneController;

Route::prefix('mediahub/porn')->name('mediahub.porn.')->group(function () {
    Route::get('/', [PornSceneController::class, 'index'])->name('index');
    Route::get('/stashids', [PornSceneController::class, 'stashidList'])->name('stashidList');
    Route::get('/stashid/{stashid}/torrents', [PornSceneController::class, 'torrentsByStashId'])->name('torrentsByStashId');
    Route::get('/{id}', [PornSceneController::class, 'show'])->name('show');
    Route::get('/grid/{stashid}', [PornSceneController::class, 'gridByStashId'])->name('gridByStashId');
    Route::post('/stashdb', [PornSceneController::class, 'storeByStashId'])->name('storeByStashId');
    Route::get('/studio/{id}', [PornSceneController::class, 'studioResults'])->name('studio');
    Route::get('/performer/{id}', [PornSceneController::class, 'performerResults'])->name('performer');
});

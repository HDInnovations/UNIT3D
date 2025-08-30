<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MediaHub\PornSceneController;

Route::prefix('mediahub/porn')->name('mediahub.porn.')->group(function () {
    Route::get('/', [PornSceneController::class, 'index'])->name('index');
    Route::get('/{id}', [PornSceneController::class, 'show'])->name('show');
});

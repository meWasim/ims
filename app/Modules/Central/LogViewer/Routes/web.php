<?php

use App\Modules\Central\LogViewer\Livewire\LogViewerIndex;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'can:logs.view'])->group(function () {

    Route::get(
        'log-viewers',
        LogViewerIndex::class
    )->name('log-viewers.index');

});

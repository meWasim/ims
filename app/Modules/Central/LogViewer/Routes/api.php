<?php

use App\Modules\Central\LogViewer\Controllers\Api\LogViewerController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')
    ->middleware('auth:sanctum')
    ->name('api.')
    ->group(function () {

        Route::apiResource(
            'log-viewers',
            LogViewerController::class
        );

    });

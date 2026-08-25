<?php

use App\Modules\Central\RolePermission\Controllers\Api\RolePermissionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')
    ->name('api.')
    ->group(function () {

    Route::apiResource(
        'role-permissions',
        RolePermissionController::class
    );

});

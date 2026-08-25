<?php

use App\Modules\Central\RolePermission\Controllers\Web\RolePermissionController;
use App\Modules\Central\RolePermission\Livewire\RolePermissionIndex;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {

    Route::get(
        'roles',
        RolePermissionIndex::class
    )->name('roles.index');

});

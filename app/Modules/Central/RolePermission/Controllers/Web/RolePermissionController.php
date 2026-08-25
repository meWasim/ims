<?php

namespace App\Modules\Central\RolePermission\Controllers\Web;

use App\Modules\Central\RolePermission\DTOs\CreateRolePermissionDTO;
use App\Modules\Central\RolePermission\DTOs\UpdateRolePermissionDTO;
use App\Modules\Central\RolePermission\Requests\StoreRolePermissionRequest;
use App\Modules\Central\RolePermission\Requests\UpdateRolePermissionRequest;
use App\Modules\Central\RolePermission\Services\RolePermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RolePermissionController
{
    public function __construct(
        private readonly RolePermissionService $service
    ) {
    }

    public function index(): View
    {
        return view(
            'role-permissions.index',
            [
                'rolePermissions' => $this->service->all(),
            ]
        );
    }

    public function create(): View
    {
        return view(
            'role-permissions.create'
        );
    }

    public function store(
        StoreRolePermissionRequest $request
    ): RedirectResponse {
        $this->service->create(
            CreateRolePermissionDTO::fromArray(
                $request->validated()
            )
        );

        return redirect()->route(
            'role-permissions.index'
        );
    }

    public function show(
        int|string $id
    ): View {
        return view(
            'role-permissions.show',
            [
                'data' => $this->service->find($id),
            ]
        );
    }

    public function edit(
        int|string $id
    ): View {
        return view(
            'role-permissions.edit',
            [
                'data' => $this->service->find($id),
            ]
        );
    }

    public function update(
        UpdateRolePermissionRequest $request,
        int|string $id
    ): RedirectResponse {
        $this->service->update(
            $id,
            UpdateRolePermissionDTO::fromArray(
                $request->validated()
            )
        );

        return redirect()->route(
            'role-permissions.index'
        );
    }

    public function destroy(
        int|string $id
    ): RedirectResponse {
        $this->service->delete($id);

        return redirect()->route(
            'role-permissions.index'
        );
    }
}

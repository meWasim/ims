<?php

namespace App\Modules\Central\RolePermission\Controllers\Api;

use App\Modules\Central\RolePermission\DTOs\CreateRolePermissionDTO;
use App\Modules\Central\RolePermission\DTOs\UpdateRolePermissionDTO;
use App\Modules\Central\RolePermission\Requests\StoreRolePermissionRequest;
use App\Modules\Central\RolePermission\Requests\UpdateRolePermissionRequest;
use App\Modules\Central\RolePermission\Resources\RolePermissionResource;
use App\Modules\Central\RolePermission\Services\RolePermissionService;
use Illuminate\Http\JsonResponse;

class RolePermissionController
{
    public function __construct(
        private readonly RolePermissionService $service
    ) {
    }

    public function index()
    {
        return RolePermissionResource::collection(
            $this->service->all()
        );
    }

    public function store(
        StoreRolePermissionRequest $request
    ): JsonResponse {
        $model = $this->service->create(
            CreateRolePermissionDTO::fromArray(
                $request->validated()
            )
        );

        return (new RolePermissionResource($model))
            ->response()
            ->setStatusCode(201);
    }

    public function show(
        int|string $id
    ): RolePermissionResource {
        return new RolePermissionResource(
            $this->service->find($id)
        );
    }

    public function update(
        UpdateRolePermissionRequest $request,
        int|string $id
    ): RolePermissionResource {
        return new RolePermissionResource(
            $this->service->update(
                $id,
                UpdateRolePermissionDTO::fromArray(
                    $request->validated()
                )
            )
        );
    }

    public function destroy(
        int|string $id
    ): JsonResponse {
        $this->service->delete($id);

        return response()->json([
            'message' => 'RolePermission deleted successfully.',
        ]);
    }
}

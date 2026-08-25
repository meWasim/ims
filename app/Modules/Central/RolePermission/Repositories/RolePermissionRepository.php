<?php

namespace App\Modules\Central\RolePermission\Repositories;

use App\Modules\Central\RolePermission\Contracts\RolePermissionRepositoryInterface;
use App\Modules\Central\RolePermission\DTOs\CreateRolePermissionDTO;
use App\Modules\Central\RolePermission\DTOs\UpdateRolePermissionDTO;
use App\Modules\Central\RolePermission\Models\RolePermission;

class RolePermissionRepository implements RolePermissionRepositoryInterface
{
    public function all()
    {
        return RolePermission::query()->get();
    }

    public function find(
        int|string $id
    ) {
        return RolePermission::query()
            ->findOrFail($id);
    }

    public function create(
        CreateRolePermissionDTO $dto
    ) {
        return RolePermission::query()->create(
            $dto->toArray()
        );
    }

    public function update(
        int|string $id,
        UpdateRolePermissionDTO $dto
    ) {
        $model = $this->find($id);

        $model->update(
            $dto->toArray()
        );

        return $model->refresh();
    }

    public function delete(
        int|string $id
    ): bool {
        return (bool) $this
            ->find($id)
            ->delete();
    }
}

<?php

namespace App\Modules\Central\RolePermission\Services;

use App\Modules\Central\RolePermission\Contracts\RolePermissionRepositoryInterface;
use App\Modules\Central\RolePermission\DTOs\CreateRolePermissionDTO;
use App\Modules\Central\RolePermission\DTOs\UpdateRolePermissionDTO;

class RolePermissionService
{
    public function __construct(
        private readonly RolePermissionRepositoryInterface $repository
    ) {
    }

    public function all()
    {
        return $this->repository->all();
    }

    public function find(
        int|string $id
    ) {
        return $this->repository->find($id);
    }

    public function create(
        CreateRolePermissionDTO $dto
    ) {
        return $this->repository->create($dto);
    }

    public function update(
        int|string $id,
        UpdateRolePermissionDTO $dto
    ) {
        return $this->repository->update(
            $id,
            $dto
        );
    }

    public function delete(
        int|string $id
    ): bool {
        return $this->repository->delete($id);
    }
}

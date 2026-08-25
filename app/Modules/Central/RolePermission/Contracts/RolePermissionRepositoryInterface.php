<?php

namespace App\Modules\Central\RolePermission\Contracts;

use App\Modules\Central\RolePermission\DTOs\CreateRolePermissionDTO;
use App\Modules\Central\RolePermission\DTOs\UpdateRolePermissionDTO;

interface RolePermissionRepositoryInterface
{
    public function all();

    public function find(
        int|string $id
    );

    public function create(
        CreateRolePermissionDTO $dto
    );

    public function update(
        int|string $id,
        UpdateRolePermissionDTO $dto
    );

    public function delete(
        int|string $id
    ): bool;
}

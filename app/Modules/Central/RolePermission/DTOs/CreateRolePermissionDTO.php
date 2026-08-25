<?php

namespace App\Modules\Central\RolePermission\DTOs;

final readonly class CreateRolePermissionDTO
{
    public function __construct(
        public array $data,
    ) {
    }

    public static function fromArray(
        array $data
    ): self {
        return new self($data);
    }

    public function toArray(): array
    {
        return $this->data;
    }
}

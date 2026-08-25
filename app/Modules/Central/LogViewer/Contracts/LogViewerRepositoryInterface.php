<?php

namespace App\Modules\Central\LogViewer\Contracts;

use App\Modules\Central\LogViewer\DTOs\CreateLogViewerDTO;
use App\Modules\Central\LogViewer\DTOs\UpdateLogViewerDTO;

interface LogViewerRepositoryInterface
{
    public function all();

    public function find(
        int|string $id
    );

    public function create(
        CreateLogViewerDTO $dto
    );

    public function update(
        int|string $id,
        UpdateLogViewerDTO $dto
    );

    public function delete(
        int|string $id
    ): bool;
}

<?php

namespace App\Modules\Central\LogViewer\Repositories;

use App\Modules\Central\LogViewer\Contracts\LogViewerRepositoryInterface;
use App\Modules\Central\LogViewer\DTOs\CreateLogViewerDTO;
use App\Modules\Central\LogViewer\DTOs\UpdateLogViewerDTO;
use App\Modules\Central\LogViewer\Models\LogViewer;

class LogViewerRepository implements LogViewerRepositoryInterface
{
    public function all()
    {
        return LogViewer::query()->get();
    }

    public function find(
        int|string $id
    ) {
        return LogViewer::query()
            ->findOrFail($id);
    }

    public function create(
        CreateLogViewerDTO $dto
    ) {
        return LogViewer::query()->create(
            $dto->toArray()
        );
    }

    public function update(
        int|string $id,
        UpdateLogViewerDTO $dto
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

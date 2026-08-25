<?php

namespace App\Modules\Central\LogViewer\Controllers\Api;

use App\Modules\Central\LogViewer\DTOs\CreateLogViewerDTO;
use App\Modules\Central\LogViewer\DTOs\UpdateLogViewerDTO;
use App\Modules\Central\LogViewer\Requests\StoreLogViewerRequest;
use App\Modules\Central\LogViewer\Requests\UpdateLogViewerRequest;
use App\Modules\Central\LogViewer\Resources\LogViewerResource;
use App\Modules\Central\LogViewer\Services\LogViewerService;
use Illuminate\Http\JsonResponse;

class LogViewerController
{
    public function __construct(
        private readonly LogViewerService $service
    ) {
    }

    public function index()
    {
        return LogViewerResource::collection(
            $this->service->all()
        );
    }

    public function store(
        StoreLogViewerRequest $request
    ): JsonResponse {
        $model = $this->service->create(
            CreateLogViewerDTO::fromArray(
                $request->validated()
            )
        );

        return (new LogViewerResource($model))
            ->response()
            ->setStatusCode(201);
    }

    public function show(
        int|string $id
    ): LogViewerResource {
        return new LogViewerResource(
            $this->service->find($id)
        );
    }

    public function update(
        UpdateLogViewerRequest $request,
        int|string $id
    ): LogViewerResource {
        return new LogViewerResource(
            $this->service->update(
                $id,
                UpdateLogViewerDTO::fromArray(
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
            'message' => 'LogViewer deleted successfully.',
        ]);
    }
}

<?php

namespace App\Modules\Central\LogViewer\Controllers\Web;

use App\Modules\Central\LogViewer\DTOs\CreateLogViewerDTO;
use App\Modules\Central\LogViewer\DTOs\UpdateLogViewerDTO;
use App\Modules\Central\LogViewer\Requests\StoreLogViewerRequest;
use App\Modules\Central\LogViewer\Requests\UpdateLogViewerRequest;
use App\Modules\Central\LogViewer\Services\LogViewerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LogViewerController
{
    public function __construct(
        private readonly LogViewerService $service
    ) {
    }

    public function index(): View
    {
        return view(
            'log-viewers.index',
            [
                'logViewers' => $this->service->all(),
            ]
        );
    }

    public function create(): View
    {
        return view(
            'log-viewers.create'
        );
    }

    public function store(
        StoreLogViewerRequest $request
    ): RedirectResponse {
        $this->service->create(
            CreateLogViewerDTO::fromArray(
                $request->validated()
            )
        );

        return redirect()->route(
            'log-viewers.index'
        );
    }

    public function show(
        int|string $id
    ): View {
        return view(
            'log-viewers.show',
            [
                'data' => $this->service->find($id),
            ]
        );
    }

    public function edit(
        int|string $id
    ): View {
        return view(
            'log-viewers.edit',
            [
                'data' => $this->service->find($id),
            ]
        );
    }

    public function update(
        UpdateLogViewerRequest $request,
        int|string $id
    ): RedirectResponse {
        $this->service->update(
            $id,
            UpdateLogViewerDTO::fromArray(
                $request->validated()
            )
        );

        return redirect()->route(
            'log-viewers.index'
        );
    }

    public function destroy(
        int|string $id
    ): RedirectResponse {
        $this->service->delete($id);

        return redirect()->route(
            'log-viewers.index'
        );
    }
}

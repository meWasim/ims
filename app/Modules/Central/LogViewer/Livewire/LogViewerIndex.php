<?php

namespace App\Modules\Central\LogViewer\Livewire;

use App\Modules\Central\LogViewer\Services\LogViewerService;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class LogViewerIndex extends Component
{
    public array $logs = [];

    public ?array $selectedLog = null;

    public bool $showDetails = false;

    public function mount(): void
    {
        $this->refreshLogs();
    }

    public function render()
    {
        return view('log-viewers::index');
    }

    public function refreshLogs(): void
    {
        $this->logs = app(LogViewerService::class)->recentLogs();
        $this->selectedLog = null;
        $this->showDetails = false;
    }

    public function viewLog(int $index): void
    {
        abort_unless(isset($this->logs[$index]), 404);

        $this->selectedLog = $this->logs[$index];
        $this->showDetails = true;
    }

    public function clearLogs(): void
    {
        Gate::authorize('logs.delete');

        $clearedFiles = app(LogViewerService::class)->clearLogs();

        $this->refreshLogs();

        session()->flash('success', "Cleared {$clearedFiles} log file(s).");
    }

    public function closeDetails(): void
    {
        $this->showDetails = false;
        $this->selectedLog = null;
    }
}

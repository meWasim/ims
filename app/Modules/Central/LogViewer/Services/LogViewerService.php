<?php

namespace App\Modules\Central\LogViewer\Services;

use App\Modules\Central\LogViewer\Contracts\LogViewerRepositoryInterface;
use App\Modules\Central\LogViewer\DTOs\CreateLogViewerDTO;
use App\Modules\Central\LogViewer\DTOs\UpdateLogViewerDTO;
use Illuminate\Support\Facades\File;

class LogViewerService
{
    public function __construct(
        private readonly LogViewerRepositoryInterface $repository
    ) {}

    public function all()
    {
        return $this->repository->all();
    }

    /**
     * Return the newest application log entries without loading entire log files.
     *
     * @return array<int, array{date: string, level: string, message: string}>
     */
    public function recentLogs(int $limit = 100): array
    {
        $files = collect(File::glob(storage_path('logs/*.log')) ?: [])
            ->filter(fn (string $path): bool => File::isFile($path))
            ->sortByDesc(fn (string $path): int => File::lastModified($path))
            ->take(3);

        $logs = [];

        foreach ($files as $file) {
            $logs = [...$logs, ...$this->parseLogFile($file)];
        }

        usort($logs, fn (array $first, array $second): int => strtotime($second['date']) <=> strtotime($first['date']));

        return array_slice($logs, 0, $limit);
    }

    /**
     * Permanently clear all Laravel application log files.
     */
    public function clearLogs(): int
    {
        $clearedFiles = 0;

        foreach (File::glob(storage_path('logs/*.log')) ?: [] as $file) {
            if (File::isFile($file) && File::put($file, '', true) !== false) {
                $clearedFiles++;
            }
        }

        return $clearedFiles;
    }

    /**
     * @return array<int, array{date: string, level: string, message: string}>
     */
    private function parseLogFile(string $path): array
    {
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            return [];
        }

        $size = filesize($path) ?: 0;
        $bytesToRead = min($size, 512 * 1024);

        fseek($handle, -$bytesToRead, SEEK_END);
        $contents = stream_get_contents($handle) ?: '';
        fclose($handle);

        // The first line may be a partial entry when reading the end of a large file.
        if ($size > $bytesToRead) {
            $contents = (string) strstr($contents, "\n");
        }

        $logs = [];
        $currentLog = null;

        foreach (preg_split('/\r?\n/', $contents) ?: [] as $line) {
            if (preg_match('/^\[(?<date>[^\]]+)\]\s+\S+\.(?<level>[A-Z]+):\s?(?<message>.*)$/', $line, $matches)) {
                if ($currentLog !== null) {
                    $logs[] = $currentLog;
                }

                $currentLog = [
                    'date' => $matches['date'],
                    'level' => strtolower($matches['level']),
                    'message' => $matches['message'],
                ];

                continue;
            }

            if ($currentLog !== null) {
                $currentLog['message'] .= "\n{$line}";
            }
        }

        if ($currentLog !== null) {
            $logs[] = $currentLog;
        }

        return $logs;
    }

    public function find(
        int|string $id
    ) {
        return $this->repository->find($id);
    }

    public function create(
        CreateLogViewerDTO $dto
    ) {
        return $this->repository->create($dto);
    }

    public function update(
        int|string $id,
        UpdateLogViewerDTO $dto
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

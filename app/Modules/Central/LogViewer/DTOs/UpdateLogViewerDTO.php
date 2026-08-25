<?php

namespace App\Modules\Central\LogViewer\DTOs;

final readonly class UpdateLogViewerDTO
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

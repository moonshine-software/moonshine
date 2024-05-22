<?php

declare(strict_types=1);

namespace MoonShine\DTOs;

use Illuminate\Contracts\Support\Arrayable;
use MoonShine\Support\MoonShineComponentAttributeBag;

final readonly class FileItem implements Arrayable
{
    public function __construct(
        private string $fullPath,
        private string $rawValue,
        private string $name,
        private MoonShineComponentAttributeBag $attributes = new MoonShineComponentAttributeBag(),
    )
    {
    }

    public function getFullPath(): string
    {
        return $this->fullPath;
    }

    public function getRawValue(): string
    {
        return $this->rawValue;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAttributes(): MoonShineComponentAttributeBag
    {
        return $this->attributes;
    }

    public function toArray(): array
    {
        return [
            'full_path' => $this->getFullPath(),
            'raw_value' => $this->getRawValue(),
            'name' => $this->getName(),
            'attributes' => $this->getAttributes()
        ];
    }
}

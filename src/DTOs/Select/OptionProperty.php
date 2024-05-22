<?php

declare(strict_types=1);

namespace MoonShine\DTOs\Select;

use Illuminate\Contracts\Support\Arrayable;

final readonly class OptionProperty implements Arrayable
{
    public function __construct(
        private ?string $image = null,
    ) {
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function toArray(): array
    {
        return [
            'image' => $this->getImage(),
        ];
    }
}

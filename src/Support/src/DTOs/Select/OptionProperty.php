<?php

declare(strict_types=1);

namespace MoonShine\Support\DTOs\Select;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, mixed>
 */
final readonly class OptionProperty implements Arrayable
{
    public function __construct(
        private null|string|OptionImage $image = null,
    ) {
    }

    public function getImage(): ?OptionImage
    {
        if (\is_string($this->image)) {
            return new OptionImage(
                src: $this->image
            );
        }

        return $this->image;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'image' => $this->getImage()?->toArray(),
        ];
    }
}

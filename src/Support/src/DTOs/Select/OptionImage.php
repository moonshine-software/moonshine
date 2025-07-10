<?php

declare(strict_types=1);

namespace MoonShine\Support\DTOs\Select;

use Illuminate\Contracts\Support\Arrayable;
use MoonShine\Support\Enums\ObjectFit;

/**
 * @implements Arrayable<string, string|int>
 */
final readonly class OptionImage implements Arrayable
{
    public function __construct(
        private string $src,
        private int $width = 10,
        private int $height = 10,
        private ObjectFit $objectFit = ObjectFit::COVER,
    ) {
    }

    public function getSrc(): string
    {
        return $this->src;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getObjectFit(): string
    {
        return $this->objectFit->value;
    }

    /**
     * @return array<string, string|int>
     */
    public function toArray(): array
    {
        return [
            'src' => $this->getSrc(),
            'width' => $this->getWidth(),
            'height' => $this->getHeight(),
            'objectFit' => $this->getObjectFit(),
        ];
    }
}

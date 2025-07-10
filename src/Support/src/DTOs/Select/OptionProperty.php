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

    public function getImage(): null|string|OptionImage
    {
        return $this->image;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $image = $this->getImage();

        if ($image instanceof OptionImage) {
            $image = $image->toArray();
        }

        return [
            'image' => $image,
        ];
    }
}

<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Scout;

use Illuminate\Contracts\Support\Arrayable;

final readonly class SearchableResponse implements Arrayable
{
    public function __construct(
        protected string $group,
        protected string $title,
        protected string $url,
        protected ?string $preview = null,
        protected ?string $image = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'group' => $this->group,
            'url' => $this->url,
            'image' => $this->image ?? '',
            'title' => $this->title,
            'preview' => $this->preview ?? '',
        ];
    }
}

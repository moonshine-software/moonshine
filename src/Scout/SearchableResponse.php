<?php

declare(strict_types=1);

namespace MoonShine\Scout;

use Illuminate\Contracts\Support\Arrayable;

final class SearchableResponse implements Arrayable
{
    public function __construct(
        protected readonly string $group,
        protected readonly string $title,
        protected readonly string $url,
        protected readonly ?string $preview = null,
        protected readonly ?string $image = null,
    )
    {
    }

    public function toArray(): array
    {
        return [
            'group' => $this->group,
            'url' => $this->url,
            'image' => $this->image,
            'title' => $this->title,
            'preview' => $this->preview,
        ];
    }
}

<?php

declare(strict_types=1);

namespace MoonShine\Support\Attributes;

use Attribute;
use Illuminate\Support\Arr;

#[Attribute(Attribute::TARGET_METHOD)]
class SearchUsingFullText
{
    public array $columns = [];

    public array $options = [];

    public function __construct(array|string $columns, array $options = [])
    {
        $this->columns = Arr::wrap($columns);
        $this->options = Arr::wrap($options);
    }
}

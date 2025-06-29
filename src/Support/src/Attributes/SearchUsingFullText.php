<?php

declare(strict_types=1);

namespace MoonShine\Support\Attributes;

use Attribute;
use Illuminate\Support\Arr;

#[Attribute(Attribute::TARGET_METHOD)]
class SearchUsingFullText
{
    /**
     * @var string[]
     */
    public array $columns = [];

    /**
     * @var string[]
     */
    public array $options = [];

    /**
     * @param  string[]|string  $columns
     * @param  string[]  $options
     */
    public function __construct(array|string $columns, array $options = [])
    {
        /** @phpstan-ignore assign.propertyType */
        $this->columns = Arr::wrap($columns);
        /** @phpstan-ignore assign.propertyType */
        $this->options = Arr::wrap($options);
    }
}

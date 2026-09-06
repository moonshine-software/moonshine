<?php

declare(strict_types=1);

namespace MoonShine\Support\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class SearchUsingFullText
{
    /**
     * @var string[]
     */
    public array $columns = [];

    /**
     * @param  string[]|string  $columns
     * @param  string[]  $options
     */
    public function __construct(array|string $columns, public array $options = [])
    {
        $this->columns = (array) $columns;
    }
}

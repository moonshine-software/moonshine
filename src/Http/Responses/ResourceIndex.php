<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Responses;

use JsonSerializable;
use Leeto\MoonShine\Resources\Resource;
use Leeto\MoonShine\Table\Table;
use Leeto\MoonShine\Traits\Makeable;

final class ResourceIndex implements JsonSerializable
{
    use Makeable;

    public function __construct(
        protected Resource $resource,
        protected Table $table
    ) {
    }

    /**
     * @return Resource
     */
    public function resource(): Resource
    {
        return $this->resource;
    }

    /**
     * @return Table
     */
    public function table(): Table
    {
        return $this->table;
    }


    public function jsonSerialize(): array
    {
        return [
            'title' => $this->resource()->title(),
            'resource' => [
                'uriKey' => $this->resource()->uriKey(),
                'policies' => $this->resource()->policies(),
                'table' => $this->table()
            ]
        ];
    }
}

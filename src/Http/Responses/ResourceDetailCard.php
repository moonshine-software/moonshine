<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Responses;

use JsonSerializable;
use Leeto\MoonShine\DetailCard\DetailCard;
use Leeto\MoonShine\Resources\Resource;
use Leeto\MoonShine\Traits\Makeable;

final class ResourceDetailCard implements JsonSerializable
{
    use Makeable;

    public function __construct(
        protected Resource $resource,
        protected DetailCard $card
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
     * @return DetailCard
     */
    public function card(): DetailCard
    {
        return $this->card;
    }


    public function jsonSerialize(): array
    {
        return [
            'title' => $this->resource()->title(),
            'resource' => [
                'uriKey' => $this->resource()->uriKey(),
                'id' => $this->card()->values()->getKey(),
                'policies' => $this->resource()->policies($this->card()->values()),
                'card' => $this->card()
            ]
        ];
    }
}

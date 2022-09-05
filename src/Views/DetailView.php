<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Views;

use JsonSerializable;
use Leeto\MoonShine\DetailCard\DetailCard;
use Leeto\MoonShine\Contracts\ResourceContract;
use Leeto\MoonShine\Traits\Makeable;

final class DetailView implements JsonSerializable
{
    use Makeable;

    public function __construct(
        protected ResourceContract $resource,
        protected DetailCard $card
    ) {
    }

    /**
     * @return ResourceContract
     */
    public function resource(): ResourceContract
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
        # TODO
        return [
            'title' => $this->resource()->title(),
            'resource' => [
                'uriKey' => $this->resource()->uriKey(),
                'id' => $this->card()->values()->id(),
                'card' => $this->card()
            ]
        ];
    }
}

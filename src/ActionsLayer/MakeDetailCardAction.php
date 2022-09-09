<?php

declare(strict_types=1);

namespace Leeto\MoonShine\ActionsLayer;

use Leeto\MoonShine\Contracts\Resources\ResourceContract;
use Leeto\MoonShine\Contracts\ValueEntityContract;
use Leeto\MoonShine\ViewComponents\DetailCard\DetailCard;

final class MakeDetailCardAction
{
    public function __invoke(
        ResourceContract $resource,
        ValueEntityContract $value
    ): DetailCard {
        # TODO set endpoint
        return DetailCard::make(
            $resource->fieldsCollection()->detailFields(),
            $value
        );
    }
}

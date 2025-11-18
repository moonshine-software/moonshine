<?php

declare(strict_types=1);

namespace MoonShine\Support\EventParams;

use MoonShine\Support\Enums\ListRowEventType;

/**
 * @method static static make(int|string|null $key = null, ListRowEventType $type = ListRowEventType::CHANGE)
 */
class ListRowEventParams extends EventParams
{
    public function __construct(public int|string|null $key = null, ListRowEventType $type = ListRowEventType::CHANGE)
    {
        parent::__construct([
            'key' => $key,
            'type' => $type->value,
        ]);
    }

}

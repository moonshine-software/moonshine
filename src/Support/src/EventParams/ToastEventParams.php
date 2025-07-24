<?php

declare(strict_types=1);

namespace MoonShine\Support\EventParams;

use MoonShine\Support\Enums\ToastType;

/**
 * @method static static make(ToastType $type, string $message, null|int|false $duration = null)
 */
class ToastEventParams extends EventParams
{
    public function __construct(ToastType $type, string $message, null|int|false $duration = null)
    {
        parent::__construct([
            'type' => $type->value,
            'text' => $message,
            'duration' => $duration === false ? -1 : $duration,
        ]);
    }

}

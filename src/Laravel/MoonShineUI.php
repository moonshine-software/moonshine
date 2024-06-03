<?php

declare(strict_types=1);

namespace MoonShine\Laravel;

use MoonShine\Support\Enums\ToastType;

class MoonShineUI
{
    public static function toast(string $message, ToastType $type = ToastType::INFO): void
    {
        session()->flash('toast', [
            'type' => $type->value,
            'message' => $message,
        ]);
    }
}

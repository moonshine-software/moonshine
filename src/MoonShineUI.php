<?php

declare(strict_types=1);

namespace MoonShine;

class MoonShineUI
{
    public static function toast(string $message, string $type = 'info'): void
    {
        session()->flash('toast', [
            'type' => $type,
            'message' => $message
        ]);
    }
}


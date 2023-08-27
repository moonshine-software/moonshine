<?php

declare(strict_types=1);

namespace MoonShine\Traits\Controller;

use MoonShine\MoonShineUI;

trait InteractsWithUI
{
    public function toast(string $message, string $type = 'info'): void
    {
        MoonShineUI::toast($message, $type);
    }
}

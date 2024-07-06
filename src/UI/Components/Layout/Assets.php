<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use MoonShine\UI\Components\MoonShineComponent;

final class Assets extends MoonShineComponent
{
    protected string $view = 'moonshine::components.layout.assets';

    public function getTranslates(): array
    {
        return __('moonshine::ui');
    }

    protected function viewData(): array
    {
        return [
            'assets' => moonshineAssets()->toHtml(),
            'colors' => moonshineColors()->toHtml(),
        ];
    }
}

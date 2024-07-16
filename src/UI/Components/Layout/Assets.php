<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use MoonShine\UI\Components\MoonShineComponent;

final class Assets extends MoonShineComponent
{
    protected string $view = 'moonshine::components.layout.assets';

    public function getTranslates(): array
    {
        return $this->core->getTranslator()->all();
    }

    protected function viewData(): array
    {
        return [
            'assets' => $this->assetManager->toHtml(),
            'colors' => $this->colorManager->toHtml(),
        ];
    }
}

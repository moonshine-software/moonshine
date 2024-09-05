<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\UI\Components\MoonShineComponent;

/**
 * @method static static make()
 */
final class Assets extends MoonShineComponent
{
    protected string $view = 'moonshine::components.layout.assets';

    public function getTranslates(): array
    {
        return $this->getCore()->getTranslator()->all();
    }

    protected function viewData(): array
    {
        return [
            'assets' => $this->getAssetManager()->toHtml(),
            'colors' => $this->getCore()->getContainer(ColorManagerContract::class)->toHtml(),
        ];
    }
}

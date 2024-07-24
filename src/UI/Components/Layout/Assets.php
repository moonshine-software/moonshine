<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use MoonShine\UI\Components\MoonShineComponent;

/**
 * @method static static make(string $assets, string $colors)
 */
final class Assets extends MoonShineComponent
{
    protected string $view = 'moonshine::components.layout.assets';

    public function __construct(
        public string $assets,
        public string $colors,
    )
    {
        parent::__construct();
    }

    public function getTranslates(): array
    {
        return $this->getCore()->getTranslator()->all();
    }

    protected function viewData(): array
    {
        return [
            'assets' => $this->assets,
            'colors' => $this->colors,
        ];
    }
}

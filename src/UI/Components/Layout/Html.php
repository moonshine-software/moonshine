<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use MoonShine\UI\Components\AbstractWithComponents;

final class Html extends AbstractWithComponents
{
    protected string $view = 'moonshine::components.layout.html';

    public function __construct(
        iterable $components = [],
        protected bool $withAlpineJs = false,
        protected bool $withThemes = false
    ) {
        parent::__construct($components);
    }

    public function withAlpineJs(): self
    {
        $this->withAlpineJs = true;

        return $this;
    }

    public function withThemes(): self
    {
        $this->withThemes = true;

        return $this;
    }

    protected function prepareBeforeRender(): void
    {
        if($this->withAlpineJs) {
            $this->customAttributes([
                'x-data' => '',
            ]);
        }

        if($this->withThemes) {
            $this->customAttributes([
                ':class' => "\$store.darkMode.on && 'dark'",
            ]);
        }
    }
}

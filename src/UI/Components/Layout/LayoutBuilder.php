<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use MoonShine\UI\Components\AbstractWithComponents;

class LayoutBuilder extends AbstractWithComponents
{
    protected string $view = 'moonshine::components.layout.index';

    protected string $bodyClass = '';

    public function bodyClass(string $value): self
    {
        $this->bodyClass = $value;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            ...parent::viewData(),
            'bodyClass' => $this->bodyClass,
        ];
    }
}

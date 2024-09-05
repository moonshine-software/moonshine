<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use MoonShine\UI\Components\AbstractWithComponents;

class Layout extends AbstractWithComponents
{
    protected string $view = 'moonshine::components.layout.index';

    public function __construct(
        iterable $components = [],
        protected string $bodyClass = '',
    ) {
        parent::__construct($components);
    }

    public function bodyClass(string $value): static
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

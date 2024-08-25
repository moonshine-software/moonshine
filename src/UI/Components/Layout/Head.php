<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use MoonShine\UI\Components\AbstractWithComponents;

final class Head extends AbstractWithComponents
{
    protected string $view = 'moonshine::components.layout.head';

    public function __construct(
        iterable $components = [],
        private ?string $title = null,
        private ?string $bodyColor = null,
    ) {
        parent::__construct($components);
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function bodyColor(string $color): self
    {
        $this->bodyColor = $color;

        return $this;
    }

    protected function viewData(): array
    {
        return [
            ...parent::viewData(),
            'title' => $this->title,
            'bodyColor' => $this->bodyColor,
        ];
    }
}

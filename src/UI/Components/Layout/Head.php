<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use MoonShine\UI\Components\AbstractWithComponents;

final class Head extends AbstractWithComponents
{
    protected string $view = 'moonshine::components.layout.head';

    private ?string $title = null;

    private ?string $bodyColor = null;

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

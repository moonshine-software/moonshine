<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use MoonShine\UI\Components\AbstractWithComponents;

final class Head extends AbstractWithComponents
{
    protected string $view = 'moonshine::components.layout.head';

    private ?string $title = null;

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    protected function viewData(): array
    {
        return [
            ...parent::viewData(),
            'title' => $this->title ?? $this->core->getConfig()->getTitle(),
            'bodyColor' => $this->colorManager->get('body'),
        ];
    }
}

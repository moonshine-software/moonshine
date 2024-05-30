<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use MoonShine\UI\Components\AbstractWithComponents;
use MoonShine\UI\Traits\Components\WithColumnSpan;

/**
 * @method static static make(iterable $components = [], int $colSpan = 12, int $adaptiveColSpan = 12)
 */
class Column extends AbstractWithComponents
{
    use WithColumnSpan;

    protected string $view = 'moonshine::components.layout.column';

    public function __construct(
        iterable $components = [],
        int $colSpan = 12,
        int $adaptiveColSpan = 12,
    ) {
        $this->columnSpan($colSpan, $adaptiveColSpan);

        parent::__construct($components);
    }

    protected function viewData(): array
    {
        return [
            ...parent::viewData(),
            'colSpan' => $this->columnSpanValue(),
            'adaptiveColSpan' => $this->adaptiveColumnSpanValue(),
        ];
    }
}

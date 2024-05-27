<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Metrics\Wrapped;

use Closure;
use MoonShine\Core\Contracts\Fields\HasAssets;
use MoonShine\Support\Traits\WithAssets;
use MoonShine\Support\Traits\WithIcon;
use MoonShine\Support\Traits\WithLabel;
use MoonShine\UI\Components\MoonShineComponent;
use MoonShine\UI\Traits\Components\WithColumnSpan;

/**
 * @method static static make(Closure|string $label)
 */
abstract class Metric extends MoonShineComponent implements HasAssets
{
    use WithAssets;
    use WithColumnSpan;
    use WithLabel;
    use WithIcon;

    final public function __construct(Closure|string $label)
    {
        parent::__construct();

        $this->setLabel($label);
    }

    protected function prepareBeforeRender(): void
    {
        $this->customAttributes([
           ':id' => "\$id(`metrics`)",
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function systemViewData(): array
    {
        return [
            ...parent::systemViewData(),
            'label' => $this->getLabel(),
            'icon' => $this->getIcon(6, 'secondary'),
            'columnSpanValue' => $this->columnSpanValue(),
            'adaptiveColumnSpanValue' => $this->adaptiveColumnSpanValue(),
        ];
    }
}

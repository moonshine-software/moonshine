<?php

declare(strict_types=1);

namespace MoonShine\Components\Metrics\Wrapped;

use Closure;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Contracts\Fields\HasAssets;
use MoonShine\Traits\WithAssets;
use MoonShine\Traits\WithColumnSpan;
use MoonShine\Traits\WithIcon;
use MoonShine\Traits\WithLabel;

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

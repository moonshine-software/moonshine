<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Metrics\Wrapped;

use Closure;
use MoonShine\Contracts\Core\HasAssetsContract;
use MoonShine\Core\Traits\WithAssets;
use MoonShine\Support\Enums\Color;
use MoonShine\UI\Components\MoonShineComponent;
use MoonShine\UI\Traits\Components\WithColumnSpan;
use MoonShine\UI\Traits\WithIcon;
use MoonShine\UI\Traits\WithLabel;

/**
 * @method static static make(Closure|string $label)
 */
abstract class Metric extends MoonShineComponent implements HasAssetsContract
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
            'icon' => $this->getIcon(6, Color::SECONDARY),
            'columnSpanValue' => $this->getColumnSpanValue(),
            'adaptiveColumnSpanValue' => $this->getAdaptiveColumnSpanValue(),
        ];
    }
}

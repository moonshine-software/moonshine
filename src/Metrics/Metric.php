<?php

declare(strict_types=1);

namespace MoonShine\Metrics;

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
        $this->setLabel($label);
    }

    protected function afterMake(): void
    {
        if ($this->getAssets()) {
            moonshineAssets()->add($this->getAssets());
        }
    }

    public function id(string $index = null): string
    {
        return (string) str($this->label())
            ->slug('_')
            ->when(! is_null($index), fn ($str) => $str->append('_' . $index));
    }

    protected function viewData(): array
    {
        return [
            'element' => $this,
        ];
    }
}

<?php

declare(strict_types=1);

namespace MoonShine\Metrics;

use Closure;
use Illuminate\Contracts\View\View;
use MoonShine\Contracts\Fields\HasAssets;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithAssets;
use MoonShine\Traits\WithColumnSpan;
use MoonShine\Traits\WithIcon;
use MoonShine\Traits\WithLabel;
use MoonShine\Traits\WithView;

/**
 * @method static static make(Closure|string $label)
 */
abstract class Metric implements MoonShineRenderable, HasAssets
{
    use Makeable;
    use WithAssets;
    use WithView;
    use WithColumnSpan;
    use WithLabel;
    use WithIcon;

    final public function __construct(Closure|string $label)
    {
        $this->setLabel($label);
    }

    public function id(string $index = null): string
    {
        return (string) str($this->label())
            ->slug('_')
            ->when(! is_null($index), fn ($str) => $str->append('_' . $index));
    }

    protected function afterMake(): void
    {
        if ($this->getAssets()) {
            moonshineAssets()->add($this->getAssets());
        }
    }

    public function render(): View|Closure|string
    {
        return view($this->getView(), [
            'element' => $this,
        ]);
    }

    public function __toString(): string
    {
        return (string) $this->render();
    }
}

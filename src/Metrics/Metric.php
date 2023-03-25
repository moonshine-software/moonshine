<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Metrics;

use Leeto\MoonShine\Contracts\Fields\HasAssets;
use Leeto\MoonShine\Contracts\ResourceRenderable;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithAssets;
use Leeto\MoonShine\Traits\WithColumnSpan;
use Leeto\MoonShine\Traits\WithIcon;
use Leeto\MoonShine\Traits\WithLabel;
use Leeto\MoonShine\Traits\WithView;
use Leeto\MoonShine\Utilities\AssetManager;

abstract class Metric implements ResourceRenderable, HasAssets
{
    use Makeable;
    use WithAssets;
    use WithView;
    use WithColumnSpan;
    use WithLabel;
    use WithIcon;

    final public function __construct(string $label)
    {
        $this->setLabel($label);
    }

    protected function afterMake(): void
    {
        if ($this->getAssets()) {
            app(AssetManager::class)->add($this->getAssets());
        }
    }

    public function id(string $index = null): string
    {
        return (string) str($this->label())
            ->slug('_')
            ->when(! is_null($index), fn ($str) => $str->append('_'.$index));
    }

    public function name(string $index = null): string
    {
        return $this->id($index);
    }
}

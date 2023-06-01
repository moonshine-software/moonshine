<?php

declare(strict_types=1);

namespace MoonShine\Metrics;

use MoonShine\Contracts\Fields\HasAssets;
use MoonShine\Contracts\ResourceRenderable;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithAssets;
use MoonShine\Traits\WithColumnSpan;
use MoonShine\Traits\WithIcon;
use MoonShine\Traits\WithLabel;
use MoonShine\Traits\WithView;
use MoonShine\Utilities\AssetManager;

/**
 * @method static static make(string $label)
 */
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

<?php

namespace Leeto\MoonShine\Metrics;

use Leeto\MoonShine\Contracts\RenderableContract;
use Leeto\MoonShine\Traits\WithAssets;
use Leeto\MoonShine\Traits\WithView;
use Leeto\MoonShine\Utilities\AssetManager;

abstract class Metric implements RenderableContract
{
    use WithAssets, WithView;

    protected string $label;

    public static function make(...$arguments): static
    {
        $static = new static(...$arguments);

        app(AssetManager::class)->add($static->getAssets());

        return $static;
    }

    final public function __construct(string $label)
    {
        $this->setLabel($label);
    }

    public function id(string $index = null): string
    {
        return (string) str($this->label())
            ->slug('_')
            ->when(!is_null($index), fn($str) => $str->append('_' . $index));
    }

    public function name(string $index = null): string
    {
        return $this->id($index);
    }

    /**
     * @return string
     */
    public function label(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return Metric
     */
    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getView(): string
    {
        return 'moonshine::metrics.' . static::$view;
    }
}

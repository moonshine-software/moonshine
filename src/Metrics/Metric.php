<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Metrics;

use Illuminate\Contracts\View\View;
use JsonSerializable;
use Leeto\MoonShine\Contracts\Fields\HasAssets;
use Leeto\MoonShine\Contracts\Renderable;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithAssets;
use Leeto\MoonShine\Traits\WithComponent;
use Leeto\MoonShine\Utilities\AssetManager;
use Stringable;

abstract class Metric implements Renderable, HasAssets, Stringable, JsonSerializable
{
    use Makeable, WithAssets, WithComponent;

    protected string $label;

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
            ->when(!is_null($index), fn($str) => $str->append('_'.$index));
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
     * @param  string  $label
     * @return Metric
     */
    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function render(): View
    {
        return view($this->getComponent(), [
            'metric' => $this,
        ]);
    }

    public function __toString()
    {
        return (string) $this->render();
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id(),
            'name' => $this->name(),
            'label' => $this->label(),
        ];
    }
}

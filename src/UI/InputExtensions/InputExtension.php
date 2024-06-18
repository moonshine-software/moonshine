<?php

declare(strict_types=1);

namespace MoonShine\UI\InputExtensions;

use Illuminate\Support\Collection;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\Support\Traits\WithComponentAttributes;
use MoonShine\UI\Traits\WithViewRenderer;

abstract class InputExtension
{
    use WithViewRenderer;
    use WithComponentAttributes;

    protected array $xInit = [];

    protected array $xData = [];

    public function __construct(protected mixed $value = null)
    {
        $this->attributes = new MoonShineComponentAttributeBag();
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getXData(): Collection
    {
        return collect($this->xData);
    }

    public function getXInit(): Collection
    {
        return collect($this->xInit);
    }

    protected function prepareBeforeRender(): void
    {
        $view = str_contains('components.', $this->getView())
            ? $this->getView()
            : str_replace(
                'moonshine::',
                'moonshine::components.',
                $this->getView()
            );

        $this->customView($view);
    }

    protected function systemViewData(): array
    {
        return [
            'attributes' => $this->attributes(),
            'value' => $this->getValue(),
        ];
    }
}

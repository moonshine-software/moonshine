<?php

declare(strict_types=1);

namespace MoonShine\InputExtensions;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use MoonShine\Traits\WithComponentAttributes;
use MoonShine\Traits\WithView;

abstract class InputExtension
{
    use WithView;
    use WithComponentAttributes;

    protected array $xInit = [];

    protected array $xData = [];

    public function __construct(protected mixed $value = null)
    {
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function xData(): Collection
    {
        return collect($this->xData);
    }

    public function xInit(): Collection
    {
        return collect($this->xInit);
    }

    public function render(): View
    {
        $view = str_contains('components.', $this->getView())
            ? $this->getView()
            : str_replace(
                'moonshine::',
                'moonshine::components.',
                $this->getView()
            );

        return view($view, [
            'extension' => $this,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace MoonShine\UI\InputExtensions;

use Illuminate\Support\Collection;
use MoonShine\UI\Components\MoonShineComponent;

abstract class InputExtension extends MoonShineComponent
{
    /**
     * @var string[]
     */
    protected array $xInit = [];

    /**
     * @var string[]
     */
    protected array $xData = [];

    public function __construct(protected mixed $value = null)
    {
        parent::__construct();
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * @return  Collection<array-key, string>
     */
    public function getXData(): Collection
    {
        return new Collection($this->xData);
    }

    /**
     * @return  Collection<array-key, string>
     */
    public function getXInit(): Collection
    {
        return new Collection($this->xInit);
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

    /**
     * @return array<string, mixed>
     */
    protected function systemViewData(): array
    {
        return [
            ...parent::systemViewData(),
            'value' => $this->getValue(),
        ];
    }
}

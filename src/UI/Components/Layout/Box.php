<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use Closure;
use MoonShine\Support\Traits\WithLabel;
use MoonShine\UI\Components\AbstractWithComponents;

/**
 * @method static static make(Closure|string|iterable $labelOrComponents = [], iterable $components = [], bool $dark = false)
 */
class Box extends AbstractWithComponents
{
    use WithLabel;

    protected string $view = 'moonshine::components.layout.box';

    public function __construct(
        Closure|string|iterable $labelOrComponents = [],
        iterable $components = [],
        private bool $dark = false,
    ) {
        if(is_iterable($labelOrComponents)) {
            /** @var iterable $labelOrComponents */
            $components = $labelOrComponents;
        } else {
            $this->setLabel($labelOrComponents);
        }

        parent::__construct($components);
    }

    public function dark(): self
    {
        $this->dark = true;

        return $this;
    }

    public function isDark(): bool
    {
        return $this->dark;
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            ...parent::viewData(),
            'title' => $this->getLabel(),
            'dark' => $this->isDark(),
        ];
    }
}

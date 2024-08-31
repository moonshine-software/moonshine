<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use Closure;
use MoonShine\UI\Components\AbstractWithComponents;
use MoonShine\UI\Traits\WithLabel;

/**
 * @method static static make(Closure|string|iterable $labelOrComponents = [], iterable $components = [])
 */
class Box extends AbstractWithComponents
{
    use WithLabel;

    protected string $view = 'moonshine::components.layout.box';

    public function __construct(
        Closure|string|iterable $labelOrComponents = [],
        iterable $components = [],
        protected string $title = '',
        protected bool $dark = false,
    ) {
        if (is_iterable($labelOrComponents)) {
            /** @var iterable $labelOrComponents */
            $components = $labelOrComponents;
        } else {
            $this->setLabel($labelOrComponents);
        }

        if ($this->title) {
            $this->setLabel($this->title);
        }

        parent::__construct($components);
    }

    public function dark(): static
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
            'label' => $this->getLabel(),
            'dark' => $this->isDark(),
        ];
    }
}

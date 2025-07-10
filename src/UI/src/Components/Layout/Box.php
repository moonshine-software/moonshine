<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use Closure;
use Illuminate\View\ComponentSlot;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\HasIconContract;
use MoonShine\Contracts\UI\HasLabelContract;
use MoonShine\UI\Components\AbstractWithComponents;
use MoonShine\UI\Traits\WithIcon;
use MoonShine\UI\Traits\WithLabel;
use Throwable;

/**
 * @method static static make(Closure|string|iterable $labelOrComponents = [], iterable $components = [])
 */
class Box extends AbstractWithComponents implements HasIconContract, HasLabelContract
{
    use WithLabel;
    use WithIcon;

    protected string $view = 'moonshine::components.layout.box';

    /**
     * @param  (Closure(static): string)|string|iterable<array-key, ComponentContract> $labelOrComponents
     * @param  iterable<array-key, ComponentContract>  $components
     *
     * @throws Throwable
     */
    public function __construct(
        Closure|string|iterable $labelOrComponents = [],
        iterable $components = [],
        // anonymous component variables
        protected string $title = '',
        protected bool $dark = false,
    ) {
        if (is_iterable($labelOrComponents)) {
            /** @var iterable<array-key, ComponentContract> $labelOrComponents */
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
            'label' => $this->getLabel(),
            'dark' => $this->isDark(),
            'icon' => new ComponentSlot(
                $this->getIcon(6)
            ),
        ];
    }
}

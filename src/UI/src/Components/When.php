<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Closure;
use MoonShine\Contracts\Core\HasComponentsContract;
use MoonShine\UI\Traits\Components\WithComponents;

/**
 * @method static static make(Closure $condition, Closure $components, ?Closure $default = null)
 */
class When extends MoonShineComponent implements HasComponentsContract
{
    use WithComponents;

    protected string $view = 'moonshine::components.components';

    public function __construct(
        protected Closure $condition,
        Closure $components,
        protected ?Closure $default = null
    ) {
        parent::__construct();

        if (($this->condition)()) {
            $this->components = $components();
        } else {
            $this->components = \is_null($this->default) ? [] : ($this->default)();
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'components' => $this->components,
        ];
    }
}

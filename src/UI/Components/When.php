<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Closure;

/**
 * @method static static make(Closure $condition, Closure $components, ?Closure $default = null)
 */
class When extends MoonShineComponent
{
    protected string $view = 'moonshine::components.components';

    protected array $conditionComponents;

    public function __construct(
        protected Closure $condition,
        protected Closure $components,
        protected ?Closure $default = null
    ) {
        parent::__construct();

        if (($this->condition)()) {
            $this->conditionComponents = ($this->components)();
        } else {
            $this->conditionComponents = is_null($this->default) ? [] : ($this->default)();
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'components' => $this->conditionComponents,
        ];
    }
}

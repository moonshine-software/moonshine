<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

/**
 * @method static static make(Closure $condition, Closure $components, ?Closure $default = null)
 */
class When extends MoonshineComponent
{
    public function __construct(
        protected Closure $condition,
        protected Closure $components,
        protected ?Closure $default = null
    ) {
    }

    public function render(): View|Htmlable|string|Closure
    {
        $components = ($this->components)();

        if(!($this->condition)()) {
            $components = !is_null($this->default) ? ($this->default)() : [];
        }

        return $this->view('moonshine::components.empty', [
            'components' => $components
        ]);
    }
}

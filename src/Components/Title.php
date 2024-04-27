<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;
use Illuminate\View\ComponentSlot;

/** @method static static make(Closure|string|null $value, int $h = 1) */
final class Title extends MoonShineComponent
{
    protected string $view = 'moonshine::components.title';

    public function __construct(
        protected Closure|string|null $value = null,
        public int $h = 1,
    ) {
        parent::__construct();
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'slot' => new ComponentSlot(value($this->value, $this)),
        ];
    }
}

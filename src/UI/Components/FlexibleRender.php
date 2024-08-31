<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Closure;
use Illuminate\Contracts\Support\Renderable;

/**
 * @method static static make(Closure|Renderable|string $content, Closure|array $additionalData = [])
 */
final class FlexibleRender extends MoonShineComponent
{
    protected string $view = 'moonshine::components.flexible-render';

    public function __construct(
        protected Closure|Renderable|string $content,
        protected Closure|array $additionalData = [],
    ) {
        parent::__construct();
    }

    protected function prepareBeforeRender(): void
    {
        parent::prepareBeforeRender();

        if ($this->content instanceof Closure) {
            $this->content = value($this->content, $this->additionalData, $this);
        }

        if ($this->content instanceof Renderable) {
            $this->content = $this->content
                ->with(value($this->additionalData))
                ->render();
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'content' => $this->content,
        ];
    }
}

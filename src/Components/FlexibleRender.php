<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;
use Illuminate\Contracts\View\View;

/**
 * @method static static make(Closure|View|string $content, Closure|array $additionalData = [])
 */
final class FlexibleRender extends MoonShineComponent
{
    protected string $view = 'moonshine::components.flexible-render';

    public function __construct(
        protected Closure|View|string $content,
        protected Closure|array $additionalData = [],
    ) {
        parent::__construct();
    }

    protected function prepareBeforeRender(): void
    {
        parent::prepareBeforeRender();

        if(is_closure($this->content)) {
            $this->content = value($this->content, $this->additionalData, $this);
        }

        if($this->content instanceof View) {
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

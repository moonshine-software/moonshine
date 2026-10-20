<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\HtmlString;
use Illuminate\View\ComponentSlot;
use MoonShine\Support\DisplayHtml;
use MoonShine\UI\Enums\DisplayTextType;

/**
 * Prepares raw display text passed directly to anonymous Blade components.
 * Class-backed components pass prepared DisplayHtml values instead.
 *
 * @internal
 */
final class DisplayText extends MoonShineComponent
{
    protected string $view = 'moonshine::components.display-text';

    public function __construct(
        protected mixed $value = '',
        protected ?bool $escape = null,
        protected DisplayTextType $displayType = DisplayTextType::LABEL,
        protected mixed $fallback = '',
    ) {
        parent::__construct();
    }

    /**
     * The prepared text is returned as is, so labels do not render a view each time.
     * Blade outputs Htmlable components with toHtml(). DisplayHtml itself is not Renderable,
     * because Laravel turns Renderable view data into plain strings, which would be escaped again.
     */
    protected function resolveRender(): Renderable
    {
        return new class ($this->resolveContent()->toHtml()) extends HtmlString implements Renderable {
            public function render(): string
            {
                return $this->toHtml();
            }
        };
    }

    protected function viewData(): array
    {
        return ['content' => $this->resolveContent()->toHtml()];
    }

    private function resolveContent(): DisplayHtml
    {
        $value = $this->value;

        if ($value === '' || ($value instanceof ComponentSlot && $value->isEmpty())) {
            $value = $this->fallback;
        }

        // Blade slots are trusted template output unless escaping is requested explicitly.
        if ($value instanceof ComponentSlot && $this->escape === true) {
            return DisplayHtml::make($value->toHtml(), true);
        }

        return DisplayHtml::make(
            $value,
            $this->escape ?? $this->displayType->isEscapedBy($this->getCore()->getConfig()),
        );
    }
}

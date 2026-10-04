<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Illuminate\Contracts\Support\Htmlable;
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

    protected function viewData(): array
    {
        $value = $this->value;

        if ($value === '' || ($value instanceof ComponentSlot && $value->isEmpty())) {
            $value = $this->fallback;
        }

        // Blade slots are trusted template output unless escaping is requested explicitly.
        if ($value instanceof ComponentSlot && $this->escape === true) {
            return ['content' => e($value->toHtml())];
        }

        // Other Htmlable values are already prepared.
        if ($value instanceof Htmlable) {
            return ['content' => $value->toHtml()];
        }

        return [
            'content' => DisplayHtml::make(
                $value,
                $this->escape ?? $this->displayType->isEscapedBy($this->getCore()->getConfig()),
            )->toHtml(),
        ];
    }
}

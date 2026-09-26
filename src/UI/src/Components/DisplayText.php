<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\View\ComponentSlot;
use MoonShine\Support\Stringify;
use MoonShine\UI\Enums\DisplayTextType;

/**
 * Prepares display text for both PHP and anonymous Blade components.
 *
 * @internal
 */
final class DisplayText extends MoonShineComponent
{
    protected string $view = 'moonshine::components.flexible-render';

    public function __construct(
        protected mixed $value = '',
        protected ?bool $escape = null,
        protected DisplayTextType $type = DisplayTextType::LABEL,
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
        $value = $value instanceof Htmlable ? $value : Stringify::value($value);
        $config = $this->getCore()->getConfig();
        $escape = $this->escape ?? match ($this->type) {
            DisplayTextType::LABEL => $config->isEscapeLabel(),
            DisplayTextType::HINT => $config->isEscapeHint(),
            DisplayTextType::PREFIX => $config->isEscapePrefix(),
            DisplayTextType::SUFFIX => $config->isEscapeSuffix(),
        };

        return [
            'content' => $escape
                ? e($value)
                : ($value instanceof Htmlable ? $value->toHtml() : $value),
        ];
    }
}

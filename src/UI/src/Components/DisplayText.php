<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Illuminate\Contracts\Support\Htmlable;
use MoonShine\Support\Stringify;

/**
 * Prepares display text for both PHP and anonymous Blade components.
 *
 * @internal
 */
final class DisplayText extends MoonShineComponent
{
    protected string $view = 'moonshine::components.flexible-render';

    /** @param 'label'|'hint'|'prefix'|'suffix' $type */
    public function __construct(
        protected mixed $value = '',
        protected ?bool $escape = null,
        protected string $type = 'label',
        protected mixed $fallback = '',
    ) {
        parent::__construct();
    }

    protected function viewData(): array
    {
        $value = $this->value === '' ? $this->fallback : $this->value;
        $value = $value instanceof Htmlable ? $value : Stringify::value($value);
        $config = $this->getCore()->getConfig();
        $escape = $this->escape ?? match ($this->type) {
            'hint' => $config->isEscapeHint(),
            'prefix' => $config->isEscapePrefix(),
            'suffix' => $config->isEscapeSuffix(),
            default => $config->isEscapeLabel(),
        };

        return [
            'content' => $escape
                ? e($value)
                : ($value instanceof Htmlable ? $value->toHtml() : $value),
        ];
    }
}

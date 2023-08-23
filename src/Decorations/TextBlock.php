<?php

declare(strict_types=1);

namespace MoonShine\Decorations;

use Closure;
use MoonShine\Traits\WithUniqueId;

/**
 * @method static static make(Closure|string $label, string $text)
 */
final class TextBlock extends Decoration
{
    use WithUniqueId;

    protected string $view = 'moonshine::decorations.text';

    public function __construct(
        Closure|string $label,
        protected string $text
    ) {
        parent::__construct($label);
    }

    public function text(): string
    {
        return $this->text;
    }
}

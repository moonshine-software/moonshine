<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Closure;
use MoonShine\Contracts\UI\HasLabelEscapingContract;
use MoonShine\UI\Enums\DisplayTextType;
use MoonShine\UI\Traits\Components\HasDifferentHtmlTag;
use MoonShine\UI\Traits\Components\WithHeadingGradation;
use MoonShine\UI\Traits\WithLabel;

/**
 * @method static static make(Closure|string $label = '', ?int $h = null, bool $asClass = true, ?bool $escapeLabel = null)
 */
class Heading extends MoonShineComponent implements HasLabelEscapingContract
{
    use HasDifferentHtmlTag;
    use WithHeadingGradation;
    use WithLabel;

    protected string $view = 'moonshine::components.heading';

    public function __construct(
        Closure|string $label = '',
        ?int $h = null,
        bool $asClass = true,
        ?bool $escapeLabel = null,
    ) {
        $this->setDisplayEscaping(DisplayTextType::LABEL, $escapeLabel);

        parent::__construct();

        $this->setLabel($label);

        if (! \is_null($h)) {
            $this->h($h, $asClass);
        }
    }

    public function isEscapeLabel(): bool
    {
        // Headings commonly contain record data, so they ignore the global label default.
        return $this->getDisplayEscaping(DisplayTextType::LABEL) ?? true;
    }

    protected function viewData(): array
    {
        return [
            'tag' => $this->getTag(),
            'label' => $this->getLabel(),
            // Without a label the view falls back to the slot.
            'labelHtml' => $this->hasLabel() ? $this->getLabelHtml() : null,
        ];
    }
}

<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits;

use Illuminate\Contracts\Support\Htmlable;
use MoonShine\Contracts\UI\HasHintContract;
use MoonShine\Contracts\UI\HasHintEscapingContract;
use MoonShine\Contracts\UI\HasLabelContract;
use MoonShine\Contracts\UI\HasLabelEscapingContract;
use MoonShine\Support\DisplayHtml;
use MoonShine\UI\Enums\DisplayTextType;

/**
 * Prepares display text of other owners, including ones that do not implement the escaping contracts.
 */
trait ResolvesDisplayEscaping
{
    protected function isDisplayEscapedFor(DisplayTextType $type, object $owner): bool
    {
        return $type->isEscapedFor($owner, $this->getCore()->getConfig());
    }

    protected function getLabelHtmlOf(HasLabelContract $owner): Htmlable
    {
        return $owner instanceof HasLabelEscapingContract
            ? $owner->getLabelHtml()
            : DisplayHtml::make($owner->getLabel(), $this->isDisplayEscapedFor(DisplayTextType::LABEL, $owner));
    }

    protected function getHintHtmlOf(HasHintContract $owner): Htmlable
    {
        return $owner instanceof HasHintEscapingContract
            ? $owner->getHintHtml()
            : DisplayHtml::make($owner->getHint(), $this->isDisplayEscapedFor(DisplayTextType::HINT, $owner));
    }
}

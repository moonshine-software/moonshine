<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits;

use MoonShine\Contracts\UI\HasLabelEscapingContract;
use MoonShine\Contracts\UI\HasTitleEscapingContract;
use MoonShine\Support\DisplayHtml;

/**
 * Views print prepared labelHtml / titleHtml, so a label or title replaced through customView() data
 * gets its prepared HTML rebuilt instead of showing the original one.
 */
trait WithDisplayViewData
{
    /**
     * @param  array<string, mixed>  $customViewData
     * @return array<string, mixed>
     */
    protected function resolveCustomViewData(array $customViewData): array
    {
        if ($this instanceof HasLabelEscapingContract && \array_key_exists('label', $customViewData) && ! \array_key_exists('labelHtml', $customViewData)) {
            $customViewData['labelHtml'] = DisplayHtml::make($customViewData['label'], $this->isEscapeLabel());
        }

        if ($this instanceof HasTitleEscapingContract && \array_key_exists('title', $customViewData) && ! \array_key_exists('titleHtml', $customViewData)) {
            $customViewData['titleHtml'] = DisplayHtml::make($customViewData['title'], $this->isEscapeTitle());
        }

        return $customViewData;
    }
}

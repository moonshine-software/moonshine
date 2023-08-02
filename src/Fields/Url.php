<?php

declare(strict_types=1);

namespace MoonShine\Fields;

class Url extends Text
{
    protected string $type = 'url';

    protected function resolvePreview(): string
    {
        $value = $this->toValue();

        if ($this->isRawMode()) {
            return $value;
        }

        if ($value === '0' || $value === '') {
            return '';
        }

        return view('moonshine::ui.url', [
            'href' => $value,
            'value' => $value,
            'blank' => $this->isLinkBlank(),
        ])->render();
    }
}

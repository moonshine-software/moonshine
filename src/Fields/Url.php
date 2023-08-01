<?php

declare(strict_types=1);

namespace MoonShine\Fields;

class Url extends Text
{
    protected string $type = 'url';

    public function resolvePreview(): string
    {
        $value = parent::resolvePreview();

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

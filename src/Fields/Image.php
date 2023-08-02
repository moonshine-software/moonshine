<?php

declare(strict_types=1);

namespace MoonShine\Fields;

class Image extends File
{
    protected static string $view = 'moonshine::fields.image';

    protected function resolvePreview(): string
    {
        return view('moonshine::ui.image', [
            'value' => $this->prepareForView()
        ])->render();
    }
}

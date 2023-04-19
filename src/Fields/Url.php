<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;

class Url extends Text
{
    protected string $type = 'url';

    public function indexViewValue(Model $item, bool $container = true): string
    {
        if (!$value = parent::indexViewValue($item, $container)) {
            return '';
        }

        return view('moonshine::ui.url', [
            'href' => $value,
            'value' => $value,
            'blank' => $this->isLinkBlank(),
        ])->render();
    }
}

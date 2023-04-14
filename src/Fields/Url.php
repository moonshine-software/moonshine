<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;

class Url extends Text
{
    protected string $type = 'url';

    public function indexViewValue(Model $item, bool $container = true): string
    {
        return view('moonshine::ui.url', [
            'href' => parent::indexViewValue($item, $container),
            'value' => parent::indexViewValue($item, $container),
            'blank' => $this->isLinkBlank()
        ])->render();
    }
}

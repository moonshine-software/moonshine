<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Traits\Fields\WithMask;

class Url extends Field
{
    use WithMask;

    protected static string $view = 'moonshine::fields.input';

    protected static string $type = 'url';

    public function indexViewValue(Model $item, bool $container = true): string
    {
        return view('moonshine::ui.url', [
            'href' => parent::indexViewValue($item, $container),
            'value' => parent::indexViewValue($item, $container),
        ])->render();
    }
}

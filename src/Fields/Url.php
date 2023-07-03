<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;

class Url extends Text
{
    protected string $type = 'url';

    public function indexViewValue(Model $item, bool $container = true): string
    {
        $value = parent::indexViewValue(
            $item,
            $container
        );

        if ($value === '0' || $value === '') {
            return '';
        }

        if (! $container) {
            return $value;
        }

        return view('moonshine::ui.url', [
            'href' => $value,
            'value' => $value,
            'blank' => $this->isLinkBlank(),
        ])->render();
    }
}

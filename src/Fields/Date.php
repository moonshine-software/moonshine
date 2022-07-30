<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Traits\Fields\DateTrait;
use Leeto\MoonShine\Traits\Fields\WithMask;

class Date extends Field
{
    use DateTrait, WithMask;

    protected static string $view = 'input';

    protected static string $type = 'date';

    protected string $format = 'Y-m-d H:i:s';

    public function formViewValue(Model $item): string
    {
        if (!$this->getDefault() && $this->isNullable()) {
            return '';
        }

        return date('Y-m-d', strtotime($item->{$this->name()} ?? $this->getDefault()));
    }

    public function indexViewValue(Model $item, bool $container = false): string
    {
        return $item->{$this->name()}
            ? date($this->format, strtotime($item->{$this->name()}))
            : '';
    }
}

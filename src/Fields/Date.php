<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Leeto\MoonShine\Traits\Fields\DateTrait;
use Leeto\MoonShine\Traits\Fields\WithMask;

class Date extends Field
{
    use DateTrait;
    use WithMask;

    protected static string $view = 'moonshine::fields.input';

    protected static string $type = 'date';

    protected string $format = 'Y-m-d H:i:s';

    public function formViewValue(Model $item): mixed
    {

        if (!$item->{$this->name()}) {

            if (!$this->getDefault() && $this->isNullable()) {
                return '';
            }

            return $this->getDefault();
        }

        if ($item->{$this->name()} instanceof Carbon) {
            return $item->{$this->name()}->format('Y-m-d');
        }

        return date('Y-m-d', strtotime((string) $item->{$this->name()}));
    }

    public function indexViewValue(Model $item, bool $container = false): string
    {
        return $item->{$this->name()}
            ? date($this->format, strtotime((string) $item->{$this->name()}))
            : '';
    }
}

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
        $value = parent::formViewValue($item);

        if (!$value && !$this->getDefault() && $this->isNullable()) {
            return '';
        }

        if (!$value) {
            return $this->getDefault();
        }

        if ($value instanceof Carbon) {
            return $value->format('Y-m-d');
        }

        return date('Y-m-d', strtotime((string) $value));
    }

    public function indexViewValue(Model $item, bool $container = false): string
    {
        $value = parent::indexViewValue($item, $container);

        return $value ? date($this->format, strtotime((string) $value)) : '';
    }
}

<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Leeto\MoonShine\Traits\Fields\WithMask;

class Slug extends Field
{
    use WithMask;

    public static string $view = 'moonshine::fields.input';

    public static string $type = 'text';

    protected string $from;
    protected string $separator = '-';

    public function from(string $from): static
    {
        $this->from = $from;
        return $this;
    }

    public function separator(string $separator): static
    {
        $this->separator = $separator;
        return $this;
    }

    public function save(Model $item): Model
    {
        if (! $this->canSave) {
            return $item;
        }

        $item->{$this->field()} = $this->requestValue() !== false
            ? $this->requestValue()
            : (isset($this->from) ? Str::slug($item->{$this->from}, $this->separator) : '');

        return $item;
    }
}

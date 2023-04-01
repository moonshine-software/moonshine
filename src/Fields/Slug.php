<?php

namespace App\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Leeto\MoonShine\Fields\Field;

class Slug extends Field
{
    public static string $view = 'moonshine::fields.input';

    public static string $type = 'text';

    protected string $from;

    protected string $separator = '-';

    protected bool $unique = false;

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

    public function unique(): static
    {
        $this->unique = true;

        return $this;
    }

    public function save(Model $item): Model
    {
        if (! $this->canSave) {
            return $item;
        }

        $item->{$this->field()} = $this->requestValue() !== false ? $this->requestValue() : $this->generateSlug($item->{$this->from});

        if ($this->unique)
        {
            $item->{$this->field()} = $this->makeSlugUnique($item);
        }

        return $item;
    }

    private function generateSlug(string $value): string
    {
        return Str::slug($value, $this->separator);
    }

    protected function checkUnique(Model $item, string $slug): bool
    {
        return !DB::table($item->getTable())->whereNot('id', $item->id)->where($this->field(), $slug)->first();
    }

    protected function makeSlugUnique(Model $item): string
    {
        $slug = $item->{$this->field()};
        $i = 1;

        while (!$this->checkUnique($item, $slug)) {
            $slug = $item->{$this->field()}.$this->separator.$i++;
        }

        return $slug;
    }

}

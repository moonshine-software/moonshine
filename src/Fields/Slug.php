<?php

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Slug extends Text
{
    protected string $from;

    protected string $separator = '-';

    protected bool $unique = false;

    public function from(string $from): static
    {
        $this->from = $from;

        return $this;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function separator(string $separator): static
    {
        $this->separator = $separator;

        return $this;
    }

    public function getSeparator(): string
    {
        return $this->separator;
    }

    public function unique(): static
    {
        $this->unique = true;

        return $this;
    }

    public function isUnique(): bool
    {
        return $this->unique;
    }

    public function save(Model $item): Model
    {
        $item->{$this->field()} = $this->requestValue() !== false ?
            $this->requestValue()
            : $this->generateSlug($item->{$this->getFrom()});

        if ($this->isUnique()) {
            $item->{$this->field()} = $this->makeSlugUnique($item);
        }

        return $item;
    }

    private function generateSlug(string $value): string
    {
        return Str::slug($value, $this->getSeparator());
    }

    protected function checkUnique(Model $item, string $slug): bool
    {
        return ! DB::table($item->getTable())
            ->whereNot($item->getKeyName(), $item->getKey())
            ->where($this->field(), $slug)
            ->first();
    }

    protected function makeSlugUnique(Model $item): string
    {
        $slug = $item->{$this->field()};
        $i = 1;

        while (! $this->checkUnique($item, $slug)) {
            $slug = $item->{$this->field()}.$this->getSeparator().$i++;
        }

        return $slug;
    }

}

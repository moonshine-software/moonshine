<?php

namespace MoonShine\UI\Fields;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use MoonShine\UI\Collections\Fields;

class Slug extends Text
{
    protected string $from;

    protected string $separator = '-';

    protected ?string $locale = null;

    protected bool $unique = false;

    public function from(string $from): static
    {
        $this->from = $from;

        return $this;
    }

    public function live(): static
    {
        return $this->reactive(function (Fields $fields): Fields {
            $title = $fields->findByColumn($this->getFrom());

            return tap(
                $fields,
                fn ($fields) => $fields
                ->findByColumn($this->getColumn())
                ?->setValue(str($title->toValue())->slug()->value())
            );
        });
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

    protected function resolveOnApply(): ?Closure
    {
        return function ($item) {
            $item->{$this->getColumn()} = $this->getRequestValue() !== false
                ? $this->getRequestValue()
                : $this->generateSlug($item->{$this->getFrom()});

            if ($this->isUnique()) {
                $item->{$this->getColumn()} = $this->makeSlugUnique($item);
            }

            return $item;
        };
    }

    protected function generateSlug(string $value): string
    {
        return Str::slug(
            $value,
            $this->getSeparator(),
            language: $this->getLocal()
        );
    }

    public function locale(string $local): self
    {
        $this->locale = $local;

        return $this;
    }

    public function getLocal(): string
    {
        return $this->locale ?? moonshineConfig()->getLocale();
    }

    public function getSeparator(): string
    {
        return $this->separator;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function isUnique(): bool
    {
        return $this->unique;
    }

    protected function makeSlugUnique(Model $item): string
    {
        $slug = $item->{$this->getColumn()};
        $i = 1;

        while (! $this->checkUnique($item, $slug)) {
            $slug = $item->{$this->getColumn()} . $this->getSeparator() . $i++;
        }

        return $slug;
    }

    protected function checkUnique(Model $item, string $slug): bool
    {
        return ! $item->newModelQuery()
            ->whereNot($item->getKeyName(), $item->getKey())
            ->where($this->getColumn(), $slug)
            ->exists();
    }

}

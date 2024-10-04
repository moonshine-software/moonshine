<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Fields;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\UI\Fields\Text;

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
        return $this->reactive(function (FieldsContract $fields): FieldsContract {
            $title = $fields->findByColumn($this->getFrom());

            return tap(
                $fields,
                fn ($fields): ?FieldContract => $fields
                ->findByColumn($this->getColumn())
                ?->setValue(str($title->toValue())->slug($this->getSeparator())->value())
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

    public function locale(string $local): static
    {
        $this->locale = $local;

        return $this;
    }

    public function getLocale(): string
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

    protected function generateSlug(string $value): string
    {
        return Str::slug(
            $value,
            $this->getSeparator(),
            language: $this->getLocale()
        );
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
}

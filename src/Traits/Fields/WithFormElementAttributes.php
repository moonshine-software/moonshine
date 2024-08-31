<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Stringable;
use MoonShine\Fields\Field;
use MoonShine\Fields\Hidden;
use MoonShine\Support\Condition;

/** @mixin Field */
trait WithFormElementAttributes
{
    protected ?string $wrapName = null;

    protected ?string $name = null;

    protected ?string $id = null;

    protected bool $required = false;

    protected bool $disabled = false;

    protected bool $readonly = false;

    public function id(string $index = null): string
    {
        // TODO: Html id attribute will be fixed in version 3
        if ($this instanceof Hidden) {
            return $this->attributes()->get('id', '');
        }

        if ($this->id) {
            return $this->id;
        }

        return (string) str($this->name ?? $this->name())
            ->replace(['[', ']'], '_')
            ->replaceMatches('/\${index\d+}/', '')
            ->replaceMatches('/_{2,}/', '_')
            ->trim('_')
            ->snake()
            ->slug('_')
            ->when(
                ! is_null($index),
                fn (Stringable $str): Stringable => $str->append("_$index")
            );
    }

    public function name(string $index = null): string
    {
        return $this->prepareName($index);
    }

    public function wrapName(string $wrapName): static
    {
        $this->wrapName = $wrapName;

        // because showWhen can be declared after
        if ($this->showWhenState) {
            [$column, $value, $operator] = $this->showWhenData;

            $this->showWhenCondition = collect($this->showWhenCondition)
                ->reject(fn ($data, $index): bool => $data['object_id'] === spl_object_id($this))
                ->toArray();

            return $this->showWhen($column, $value, $operator);
        }

        return $this;
    }

    public function getWrapName(): ?string
    {
        return $this->wrapName;
    }

    public function nameUnDot(string $value): string
    {
        if (! str_contains($value, '.')) {
            return $value;
        }

        return str($value)->explode('.')
            ->map(static fn ($part, $index): string => $index === 0 ? $part : "[$part]")
            ->implode('');
    }

    protected function prepareName($index = null, $wrap = null): string
    {
        $wrap ??= $this->wrapName;

        if ($this->name) {
            return $this->name;
        }

        if (! $this instanceof Field) {
            return '';
        }

        return (string) str($this->column())
            ->when(
                ! is_null($wrap),
                fn (Stringable $str): Stringable => $str->prepend("$wrap.")
            )
            ->pipe(fn (Stringable $str): string => $this->nameUnDot($str->value()))
            ->when(
                $this->isGroup() || $this->getAttribute('multiple'),
                fn (Stringable $str): Stringable => $str->append(
                    "[" . ($index ?? '') . "]"
                )
            );
    }

    public function nameFrom(?string ...$values): string
    {
        return str('')
            ->pipe(static function (Stringable $str) use ($values) {
                foreach (collect($values)->filter() as $value) {
                    $str = $str->append(".$value");
                }

                return $str->trim('.');
            })
            ->pipe(fn (Stringable $str) => $this->nameUnDot($str->value()))
            ->value();
    }

    public function nameDot(): string
    {
        $name = (string) str($this->name())->replace('[]', '');

        parse_str($name, $array);

        $result = collect(Arr::dot(array_filter($array)));

        return $result->isEmpty()
            ? $name
            : (string) str($result->keys()->first());
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function setNameIndex(int|string $key, int $index = 0): static
    {
        $this->setName(
            str_replace("\${index$index}", (string) $key, $this->name())
        );

        return $this;
    }

    public function setId(string $id): static
    {
        $this->id = str($id)
            ->remove(['[', ']'])
            ->snake()
            ->value();

        return $this;
    }

    public function type(): string
    {
        return $this->attributes()->get('type', '');
    }

    public function required(Closure|bool|null $condition = null): static
    {
        $this->required = Condition::boolean($condition, true);
        $this->setAttribute('required', $this->required);

        return $this;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function disabled(Closure|bool|null $condition = null): static
    {
        $this->disabled = Condition::boolean($condition, true);
        $this->setAttribute('disabled', $this->disabled);

        return $this;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function readonly(Closure|bool|null $condition = null): static
    {
        $this->readonly = Condition::boolean($condition, true);
        $this->setAttribute('readonly', $this->readonly);

        return $this;
    }

    public function isReadonly(): bool
    {
        return $this->readonly;
    }

    public function isHidden(): bool
    {
        return $this->type() === 'hidden';
    }

    public function isFile(): bool
    {
        return $this->type() === 'file';
    }
}

<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Stringable;
use MoonShine\Helpers\Condition;

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
        if ($this->id) {
            return $this->id;
        }

        return (string) str($this->name ?? $this->name())
            ->replace(['[', ']'], '_')
            ->replaceMatches('/\${index\d+}/', '')
            ->replaceMatches('/_{2,}/', '_')
            ->trim('_')
            ->snake()
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

        return $this;
    }

    protected function nameUnDot(string $name): string
    {
        $parts = explode('.', $name);
        $count = count($parts);
        $result = $parts[0];

        for ($i = 1; $i < $count; $i++) {
            $result .= "[" . $parts[$i] . "]";
        }

        return $result;
    }

    protected function prepareName($index = null, $wrap = null): string
    {
        $wrap ??= $this->wrapName;

        if ($this->name) {
            return $this->name;
        }

        return (string) str($this->nameUnDot($this->column()))
            ->when(
                ! is_null($wrap),
                fn (Stringable $str): Stringable => $str->wrap("{$wrap}[", "]")
            )
            ->when(
                $this->isGroup() || $this->getAttribute('multiple'),
                fn (Stringable $str): Stringable => $str->append(
                    "[" . ($index ?? '') . "]"
                )
            );
    }

    protected function nameDot(): string
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
        return $this->attributes()->get('type') === 'hidden';
    }

    public function isFile(): bool
    {
        return $this->type() === 'file';
    }
}

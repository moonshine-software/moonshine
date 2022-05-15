<?php


namespace Leeto\MoonShine\Decorations;

use Leeto\MoonShine\Contracts\Components\ViewComponentContract;

class BaseDecoration implements ViewComponentContract
{
    protected string $label;

    protected array $fields;

    public static string $view;

    public static function make(...$arguments): static
    {
        return new static(...$arguments);
    }

    final public function __construct(string $label, array $fields = [])
    {
        $this->setLabel($label);
        $this->setFields($fields);
    }

    public function fields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields): static
    {
        $this->fields = $fields;

        return $this;
    }

    public function hasFields(): bool
    {
        return !empty($this->fields());
    }

    public function id(string $index = null): string
    {
        return str($this->label())->slug();
    }

    public function name(string $index = null): string
    {
        return $this->id($index);
    }

    public function label(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getView(): string
    {
        return 'moonshine::decorations.' . static::$view;
    }
}
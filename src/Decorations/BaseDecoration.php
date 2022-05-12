<?php


namespace Leeto\MoonShine\Decorations;

use JetBrains\PhpStorm\Pure;
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

    public function __construct($label, array $fields = [])
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

    #[Pure]
    public function hasFields(): bool
    {
        return !empty($this->fields());
    }

    public function id($index = null): string
    {
        return str($this->label())->slug();
    }

    public function name($index = null): string
    {
        return $this->id($index = null);
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
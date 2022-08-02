<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Decorations;

use Illuminate\Contracts\View\View;
use JsonSerializable;
use Leeto\MoonShine\Contracts\Renderable;
use Leeto\MoonShine\Fields\Field;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithView;
use Stringable;

abstract class Decoration implements Renderable, Stringable, JsonSerializable
{
    use Makeable, WithView;

    protected string $label;

    protected array $fields;

    final public function __construct(string $label, array $fields = [])
    {
        $this->setLabel($label);
        $this->setFields($fields);
    }

    /**
     * Get fields of decoration
     *
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Define fields for decoration
     *
     * @param  array  $fields
     * @return $this
     */
    public function setFields(array $fields): static
    {
        $this->fields = [];

        foreach ($fields as $field) {
            if ($field instanceof Field) {
                $this->fields[] = $field->setParents();
            }
        }

        return $this;
    }

    /**
     * Check whether, if decoration has fields
     *
     * @return bool
     */
    public function hasFields(): bool
    {
        return !empty($this->getFields());
    }

    /**
     * Get id of decoration
     *
     * @param  string|null  $index
     * @return string
     */
    public function id(string $index = null): string
    {
        return (string) str($this->label())->slug();
    }

    /**
     * Get name of decoration
     *
     * @param  string|null  $index
     * @return string
     */
    public function name(string $index = null): string
    {
        return $this->id($index);
    }

    /**
     * Get label of decoration
     *
     * @return string
     */
    public function label(): string
    {
        return $this->label;
    }

    /**
     * Define label for decoration
     *
     * @param  string  $label
     * @return $this
     */
    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function render(): View
    {
        return view($this->getView(), [
            'decoration' => $this,
        ]);
    }

    public function __toString()
    {
        return (string) $this->render();
    }

    public function jsonSerialize()
    {
        return [
            'decoration' => $this,
        ];
    }
}

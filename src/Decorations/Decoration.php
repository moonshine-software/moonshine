<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Decorations;

use Leeto\MoonShine\Contracts\HtmlViewable;
use Leeto\MoonShine\Fields\Field;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithView;

abstract class Decoration implements HtmlViewable
{
    use Makeable;
    use WithView;

    protected string $label;

    protected array $fields = [];

    public function __construct(string $label, array $fields = [])
    {
        $this->setLabel($label);
        $this->setFields($fields);
    }

    /**
     * Get fields of decoration
     *
     * @return array
     */
    public function fields(): array
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
            } else {
                $this->fields[] = $field;
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
        return ! empty($this->fields());
    }

    /**
     * Get id of decoration
     *
     * @param  string|null  $index
     * @return string
     */
    public function id(string $index = null): string
    {
        return (string) str($this->label())->slug('_');
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
}

<?php


namespace Leeto\MoonShine\Decorations;

use Leeto\MoonShine\Contracts\RenderableContract;
use Leeto\MoonShine\Fields\Field;

abstract class Decoration implements RenderableContract
{
    protected string $label;

    protected array $fields;

    public static string $view;

    /**
     * Create a decoration class: Heading, Tab ...
     *
     * @param ...$arguments $label Decoration label, will be displayed in moonshine admin panel,
     *                      $fields Array of fields will be displayed in tab
     * @return static
     */
    public static function make(...$arguments): static
    {
        return new static(...$arguments);
    }

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
    public function fields(): array
    {
        return $this->fields;
    }

    /**
     * Define fields for decoration
     *
     * @param array $fields
     * @return $this
     */
    public function setFields(array $fields): static
    {
        $this->fields = [];

        foreach ($fields as $field) {
            if($field instanceof Field) {
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
        return !empty($this->fields());
    }

    /**
     * Get id of decoration
     *
     * @param string|null $index
     * @return string
     */
    public function id(string $index = null): string
    {
        return (string) str($this->label())->slug();
    }

    /**
     * Get name of decoration
     *
     * @param string|null $index
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
     * @param string $label
     * @return $this
     */
    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get view of decoration
     *
     * @return string
     */
    public function getView(): string
    {
        return 'moonshine::decorations.' . static::$view;
    }
}

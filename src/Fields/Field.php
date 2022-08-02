<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Traits\Macroable;
use Leeto\MoonShine\Contracts\Fields\HasAssets;
use Leeto\MoonShine\Contracts\Fields\HasFields;
use Leeto\MoonShine\FormElement;
use Leeto\MoonShine\Helpers\Condition;
use Leeto\MoonShine\Traits\Fields\LinkTrait;
use Leeto\MoonShine\Traits\Fields\ShowWhen;
use Leeto\MoonShine\Traits\WithAssets;
use Leeto\MoonShine\Utilities\AssetManager;

abstract class Field extends FormElement implements HasAssets
{
    use Macroable, WithAssets, ShowWhen, LinkTrait;

    public bool $showOnIndex = true;

    public bool $showOnExport = false;

    public bool $showOnForm = true;

    protected ?Field $parent = null;

    protected string $hint = '';

    protected bool $sortable = false;

    protected bool $removable = false;

    protected function afterMake(): void
    {
        if ($this->getAssets()) {
            app(AssetManager::class)->add($this->getAssets());
        }
    }

    /**
     * Set field as visible on index page, based on condition
     *
     * @param  mixed  $condition
     * @return $this
     */
    public function showOnIndex(mixed $condition = null): static
    {
        $this->showOnIndex = Condition::boolean($condition, true);

        return $this;
    }

    /**
     * Set field as hidden on index page, based on condition
     *
     * @param  mixed  $condition
     * @return $this
     */
    public function hideOnIndex(mixed $condition = null): static
    {
        $this->showOnIndex = Condition::boolean($condition, false);

        return $this;
    }

    /**
     * Set field as visible on create/edit page, based on condition
     *
     * @param  mixed  $condition
     * @return $this
     */
    public function showOnForm(mixed $condition = null): static
    {
        $this->showOnForm = Condition::boolean($condition, true);

        return $this;
    }

    /**
     * Set field as hidden on create/edit page, based on condition
     *
     * @param  mixed  $condition
     * @return $this
     */
    public function hideOnForm(mixed $condition = null): static
    {
        $this->showOnForm = Condition::boolean($condition, false);

        return $this;
    }

    /**
     * Set field as visible in export report, based on condition
     *
     * @param  mixed  $condition
     * @return $this
     */
    public function showOnExport(mixed $condition = null): static
    {
        $this->showOnExport = Condition::boolean($condition, true);

        return $this;
    }

    /**
     * Set field as hidden in export report, based on condition
     *
     * @param  mixed  $condition
     * @return $this
     */
    public function hideOnExport(mixed $condition = null): static
    {
        $this->showOnExport = Condition::boolean($condition, false);

        return $this;
    }

    public function parent(): ?Field
    {
        return $this->parent;
    }

    public function hasParent(): bool
    {
        return $this->parent instanceof Field;
    }

    protected function setParent(Field $field): static
    {
        $this->parent = $field;

        return $this;
    }

    public function setParents(): static
    {
        if ($this instanceof HasFields) {
            $fields = [];

            foreach ($this->getFields() as $field) {
                $field = $field->setParents();

                $fields[] = $field->setParent($this);
            }

            $this->fields($fields);
        }

        return $this;
    }

    /**
     * Define a field description(hint), which will be displayed on create/edit page
     *
     * @param  string  $hint
     * @return $this
     */
    public function hint(string $hint): static
    {
        $this->hint = $hint;

        return $this;
    }

    public function getHint(): string
    {
        return $this->hint;
    }

    /**
     * Set field as removable
     *
     * @return $this
     */
    public function removable(): static
    {
        $this->removable = true;

        return $this;
    }

    public function isRemovable(): bool
    {
        return $this->removable;
    }

    /**
     * Define whether if index page can be sorted by this field
     *
     * @return $this
     */
    public function sortable(): static
    {
        $this->sortable = true;

        return $this;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function save(Model $item): Model
    {
        $item->{$this->field()} = $this->requestValue() !== false
            ? $this->requestValue()
            : ($this->isNullable() ? null : '');

        return $item;
    }
}

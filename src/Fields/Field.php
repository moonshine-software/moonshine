<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Contracts\Fields\HasAssets;
use Leeto\MoonShine\Contracts\Fields\HasFields;
use Leeto\MoonShine\Contracts\HtmlViewable;
use Leeto\MoonShine\Contracts\Fields\HasExportViewValue;
use Leeto\MoonShine\Contracts\Fields\HasFormViewValue;
use Leeto\MoonShine\Contracts\Fields\HasIndexViewValue;

use Leeto\MoonShine\Traits\Fields\FormElement;
use Leeto\MoonShine\Traits\Fields\HasCanSee;
use Leeto\MoonShine\Traits\Fields\LinkTrait;
use Leeto\MoonShine\Traits\Fields\ShowWhen;
use Leeto\MoonShine\Traits\Fields\WithHtmlAttributes;
use Leeto\MoonShine\Traits\Fields\XModel;
use Leeto\MoonShine\Helpers\Condition;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithAssets;
use Leeto\MoonShine\Traits\WithView;
use Leeto\MoonShine\Utilities\AssetManager;

abstract class Field implements HtmlViewable, HasAssets, HasExportViewValue, HasIndexViewValue, HasFormViewValue
{
    use FormElement;
    use LinkTrait;
    use Makeable;
    use ShowWhen;
    use WithAssets;
    use WithHtmlAttributes;
    use WithView;
    use XModel;
    use HasCanSee;

    public bool $showOnIndex = true;

    public bool $showOnExport = false;

    public bool $showOnForm = true;

    public bool $showOnCreateForm = true;

    public bool $showOnUpdateForm = true;

    protected Field|null $parent = null;

    protected string $hint = '';

    protected bool $sortable = false;

    protected bool $removable = false;

    protected bool $canSave = true;

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
     * Set field as show on create page, based on condition
     *
     * @param  mixed  $condition
     * @return $this
     */
    public function showOnCreate(mixed $condition = null): static
    {
        $this->showOnCreateForm = Condition::boolean($condition, true);

        return $this;
    }

    /**
     * Set field as hidden on create page, based on condition
     *
     * @param  mixed  $condition
     * @return $this
     */
    public function hideOnCreate(mixed $condition = null): static
    {
        $this->showOnCreateForm = Condition::boolean($condition, false);

        return $this;
    }

    /**
     * Set field as show on update page, based on condition
     *
     * @param  mixed  $condition
     * @return $this
     */
    public function showOnUpdate(mixed $condition = null): static
    {
        $this->showOnUpdateForm = Condition::boolean($condition, true);

        return $this;
    }

    /**
     * Set field as hidden on update page, based on condition
     *
     * @param  mixed  $condition
     * @return $this
     */
    public function hideOnUpdate(mixed $condition = null): static
    {
        $this->showOnUpdateForm = Condition::boolean($condition, false);

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

    public function canSave(mixed $condition = null): static
    {
        $this->canSave = Condition::boolean($condition, true);

        return $this;
    }

    public function parent(): Field|null
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

    public function getView(): string
    {
        return static::$view;
    }

    public function formViewValue(Model $item): mixed
    {
        if ($this->belongToOne()) {
            return $item->{$this->relation()}?->getKey() ?? $this->getDefault();
        }

        if ($this->hasRelationship()) {
            return $item->{$this->relation()} ?? $this->getDefault();
        }

        if (is_callable($this->valueCallback())) {
            return $this->valueCallback()($item) ?? $this->getDefault();
        }

        return $item->{$this->field()} ?? $this->getDefault();
    }

    public function indexViewValue(Model $item, bool $container = true): mixed
    {
        if ($this->hasRelationship()) {
            $item = $item->{$this->relation()};
        }

        if (is_callable($this->valueCallback())) {
            return $this->valueCallback()($item);
        }

        if ($this->hasRelationship()) {
            return $container ? view('moonshine::shared.badge', [
                'color' => 'purple',
                'value' => $item->{$this->resourceTitleField()} ?? '-'
            ]) : $item->{$this->resourceTitleField()} ?? '-';
        }

        return $item->{$this->field()} ?? '';
    }

    public function exportViewValue(Model $item): mixed
    {
        return $this->indexViewValue($item, false);
    }

    public function save(Model $item): Model
    {
        if(!$this->canSave) {
            return $item;
        }

        $item->{$this->field()} = $this->requestValue() !== false
            ? $this->requestValue()
            : ($this->isNullable() ? null : '');

        return $item;
    }

    public function beforeSave(Model $item): void
    {
        //
    }

    public function afterSave(Model $item): void
    {
        //
    }
}

<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Fields\HasExportViewValue;
use MoonShine\Contracts\Fields\HasFormViewValue;
use MoonShine\Contracts\Fields\HasIndexViewValue;
use MoonShine\Helpers\Condition;
use MoonShine\Traits\Fields\LinkTrait;
use MoonShine\Traits\Fields\ShowOrHide;

abstract class Field extends FormElement implements HasExportViewValue, HasIndexViewValue, HasFormViewValue
{
    use ShowOrHide;
    use LinkTrait;

    protected bool $sortable = false;

    protected bool $canSave = true;

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

    public function formViewValue(Model $item): mixed
    {
        if ($this->hasRelationship() && ! $item->relationLoaded($this->relation())) {
            $item->load($this->relation());
        }

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

    public function indexViewValue(Model $item, bool $container = true): string
    {
        if ($this->hasRelationship() && ! $item->relationLoaded($this->relation())) {
            $item->load($this->relation());
        }

        if ($this->hasRelationship()) {
            $item = $item->{$this->relation()};
        }

        if (is_callable($this->valueCallback())) {
            return (string)$this->valueCallback()($item);
        }

        if ($this->hasRelationship()) {
            return $container ? view('moonshine::ui.badge', [
                'color' => 'purple',
                'value' => $item->{$this->resourceTitleField()} ?? false,
            ])->render() : (string)($item->{$this->resourceTitleField()} ?? '');
        }

        return (string)($item->{$this->field()} ?? '');
    }

    public function exportViewValue(Model $item): string
    {
        return $this->indexViewValue($item, false);
    }

    public function canSave(mixed $condition = null): static
    {
        $this->canSave = Condition::boolean($condition, true);

        return $this;
    }

    public function isCanSave(): bool
    {
        return $this->canSave;
    }

    public function save(Model $item): Model
    {
        $item->{$this->field()} = $this->requestValue() !== false
            ? $this->requestValue()
            : ($this->isNullable() ? null : $this->getDefault());

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

<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Contracts\Fields\HasExportViewValue;
use MoonShine\Contracts\Fields\HasFormViewValue;
use MoonShine\Contracts\Fields\HasIndexViewValue;
use MoonShine\Helpers\Condition;
use MoonShine\Traits\Fields\LinkTrait;
use MoonShine\Traits\Fields\ShowOrHide;
use MoonShine\Traits\WithIsNowOnRoute;
use Throwable;

use function MoonShine\tryOrReturn;

abstract class Field extends FormElement implements
    HasExportViewValue,
    HasIndexViewValue,
    HasFormViewValue
{
    use ShowOrHide;
    use LinkTrait;
    use WithIsNowOnRoute;

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

    public function sortQuery(): string
    {
        return request()->fullUrlWithQuery([
            'order' => [
                'field' => $this->field(),
                'type' => $this->sortActive() && $this->sortType('asc') ? 'desc'
                    : 'asc',
            ],
        ]);
    }

    public function sortActive(): bool
    {
        return request()->has('order.field')
            && request('order.field') === $this->field();
    }

    public function sortType(string $type): bool
    {
        return request()->has('order.type')
            && request('order.type') === strtolower($type);
    }

    public function formViewValue(Model $item): mixed
    {
        if ($this->hasRelationship() && ! $item->relationLoaded(
                $this->relation()
            )) {
            $item->load($this->relation());
        }

        $old = old($this->nameDot());

        if ($old && (! $this->hasRelationship() || $this->belongToOne())) {
            return $old;
        }

        $default = $this instanceof HasDefaultValue
            ? $this->getDefault()
            : null;

        if ($this->belongToOne()) {
            return $item->{$this->relation()}?->getKey() ?? $default;
        }

        if ($this->hasRelationship()) {
            return $item->{$this->relation()};
        }

        if (is_callable($this->valueCallback())) {
            return $this->valueCallback()($item) ?? $default;
        }

        return $item->{$this->field()} ?? $default;
    }

    public function exportViewValue(Model $item): string
    {
        return $this->indexViewValue($item, false);
    }

    public function indexViewValue(Model $item, bool $container = true): string
    {
        if ($this->hasRelationship() && ! $item->relationLoaded(
                $this->relation()
            )) {
            $item->load($this->relation());
        }

        if ($this->hasRelationship()) {
            $item = $item->{$this->relation()};
        }

        if (is_callable($this->valueCallback())) {
            return (string) $this->valueCallback()($item);
        }

        if ($this->hasRelationship()) {
            $value = $item->{$this->resourceTitleField()} ?? false;

            $href = tryOrReturn(
                fn() => $this->resource()?->route('show', $item->getKey()),
                '',
            );

            return $container ? view('moonshine::ui.badge', [
                'color' => 'purple',
                'value' => $value ? "<a href='$href'>$value</a>" : false,
            ])->render()
                : (string) ($item->{$this->resourceTitleField()} ?? '');
        }

        return (string) ($item->{$this->field()} ?? '');
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
            : null;

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

    public function afterDelete(Model $item): void
    {
        //
    }
}

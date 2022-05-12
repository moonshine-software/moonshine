<?php

namespace Leeto\MoonShine\Fields;


use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Contracts\Components\ViewComponentContract;
use Leeto\MoonShine\Contracts\Fields\FieldHasRelationContract;

use Leeto\MoonShine\Traits\Fields\FormElementBasicTrait;
use Leeto\MoonShine\Traits\Fields\LinkTrait;
use Leeto\MoonShine\Traits\Fields\ShowWhenTrait;
use Leeto\MoonShine\Traits\Fields\XModelTrait;

abstract class BaseField implements ViewComponentContract
{
    use FormElementBasicTrait, ShowWhenTrait, XModelTrait, LinkTrait;

    protected string $hint = '';

    protected bool $sortable = false;

    protected bool $removable = false;

    public bool $showOnIndex = true;

    public bool $showOnExport = false;

    public bool $showOnForm = true;

    protected BaseField|null $parent = null;

    protected array $assets = [];

    public function parent(): BaseField|null
    {
        return $this->parent;
    }

    public function hasParent(): bool
    {
        return $this->parent instanceof BaseField;
    }

    protected function setParent(BaseField $field): static
    {
        $this->parent = $field;

        return $this;
    }

    public function getView(): string
    {
        return 'moonshine::fields.' . static::$view;
    }

    public function hidden(): static
    {
        static::$type = 'hidden';

        return $this;
    }

    public function isHidden(): bool
    {
        return static::$type === 'hidden';
    }

    public function hint(string $hint): static
    {
        $this->hint = $hint;

        return $this;
    }

    public function getHint(): string
    {
        return $this->hint;
    }

    public function removable(): static
    {
        $this->removable = true;

        return $this;
    }

    public function isRemovable(): bool
    {
        return $this->removable;
    }

    public function sortable(): static
    {
        $this->sortable = true;

        return $this;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function showOnIndex(): static
    {
        $this->showOnIndex = true;

        return $this;
    }

    public function hideOnIndex(): static
    {
        $this->showOnIndex = false;

        return $this;
    }

    public function showOnForm(): static
    {
        $this->showOnForm = true;

        return $this;
    }

    public function hideOnForm(): static
    {
        $this->showOnForm = true;

        return $this;
    }

    public function showOnExport(): static
    {
        $this->showOnExport = true;

        return $this;
    }

    public function hideOnExport(): static
    {
        $this->showOnExport = false;

        return $this;
    }

    public function getAssets(): array
    {
        return $this->assets;
    }

    public function formViewValue(Model $item): mixed
    {
        if($this instanceof FieldHasRelationContract
            && $this->isRelationToOne()
            && !$this->isRelationHasOne()) {
            return $item->{$this->relation()}?->getKey() ?? $this->getDefault();
        }

        if($this instanceof FieldHasRelationContract) {
            return $item->{$this->relation()} ?? $this->getDefault();
        }

        return $item->{$this->field()} ?? $this->getDefault();
    }

    public function indexViewValue(Model $item, bool $container = true): string
    {
        if($this instanceof FieldHasRelationContract) {
            if(!$item->{$this->relation()}) {
                return '-';
            }

            return $container ? view('moonshine::shared.badge', [
                'color' => 'purple',
                'value' => $item->{$this->relation()}->{$this->resourceTitleField()}
            ]) : $item->{$this->relation()}->{$this->resourceTitleField()};
        }

        return $item->{$this->field()};
    }

    public function exportViewValue(Model $item): string
    {
        if($this instanceof FieldHasRelationContract) {
            if(!$item->{$this->relation()}) {
                return '-';
            }

            return $item->{$this->relation()}->{$this->resourceTitleField()};
        }

        return $item->{$this->field()};
    }

    public function save(Model $item): Model
    {
        $item->{$this->field()} = $this->requestValue();

        return $item;
    }
}
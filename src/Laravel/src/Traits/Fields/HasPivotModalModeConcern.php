<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Fields;

use Closure;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\TableBuilderContract;
use MoonShine\Laravel\Buttons\BelongsToManyPivotButton;
use MoonShine\UI\Components\CardsBuilder;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\Preview;

trait HasPivotModalModeConcern
{
    protected bool $isPivotModalMode = false;

    protected bool $isPivotCardsMode = false;

    /**
     * @var (Closure(ActionButtonContract, static): ActionButtonContract)|null
     */
    protected ?Closure $modifyEditButton = null;

    /**
     * @var (Closure(ActionButtonContract, static): ActionButtonContract)|null
     */
    protected ?Closure $modifyDeleteButton = null;


    public function pivotModalMode(Closure|bool|null $condition = null): static
    {
        $this->isPivotModalMode = \is_null($condition) || value($condition, $this);

        return $this;
    }

    public function pivotCardsMode(Closure|bool|null $condition = null): static
    {
        $this->pivotModalMode($condition);
        $this->isPivotCardsMode = \is_null($condition) || value($condition, $this);

        return $this;
    }

    public function isPivotModalMode(): bool
    {
        return $this->isPivotModalMode;
    }

    public function isPivotCardsMode(): bool
    {
        return $this->isPivotCardsMode;
    }

    /**
     * @param  Closure(ActionButtonContract $button, static $ctx): ActionButtonContract  $callback
     */
    public function modifyEditButton(Closure $callback): static
    {
        $this->modifyEditButton = $callback;

        return $this;
    }

    /**
     * @param  Closure(ActionButtonContract $button, static $ctx): ActionButtonContract  $callback
     */
    public function modifyDeleteButton(Closure $callback): static
    {
        $this->modifyDeleteButton = $callback;

        return $this;
    }

    protected function getPivotModalTable(): ComponentContract
    {
        $values = $this->getCollectionValue();

        $fields = $this->getPreparedFields()
            ->prepend(
                Preview::make($this->getResourceColumnLabel(), $this->getResourceColumn(), $this->getFormattedValueCallback())
                    ->withoutWrapper()
            );

        $asyncUrl = $this->getCore()->getRouter()->getEndpoints()->withRelation(
            'belongs-to-many-pivot.list',
            resourceItem: $this->getRelatedModel()?->getKey(),
            relation: $this->getRelationName(),
            resourceUri: $this->getNowOnResource()?->getUriKey(),
            pageUri: $this->getNowOnPage()?->getUriKey()
        );

        $editButton = BelongsToManyPivotButton::for($this, update: true);

        if (! \is_null($this->modifyEditButton)) {
            $editButton = \call_user_func($this->modifyEditButton, $editButton, $this);
        }

        $deleteButton = BelongsToManyPivotButton::delete($this);

        if (! \is_null($this->modifyDeleteButton)) {
            $deleteButton = \call_user_func($this->modifyDeleteButton, $deleteButton, $this);
        }

        $buttons = [$editButton, $deleteButton];

        if ($this->isPivotCardsMode()) {
            $component = CardsBuilder::make(items: $values);
        } else {
            $component = TableBuilder::make(items: $values)->withNotFound();
        }

        return $component
            ->async($asyncUrl)
            ->when(
                ! $this->isDeduplicate(),
                static fn (TableBuilderContract $table): TableBuilderContract => $table->withoutKey(),
            )
            ->name($this->getTableComponentName())
            ->customAttributes($this->getAttributes()->jsonSerialize())
            ->fields($fields)
            ->cast($this->getResource()->getCaster())
            ->buttons([
                ...$this->getButtons(),
                ...$buttons,
            ])
            ->when(
                ! \is_null($this->modifyTable),
                fn (ComponentContract $table) => value($this->modifyTable, $table, false)
            );
    }
}

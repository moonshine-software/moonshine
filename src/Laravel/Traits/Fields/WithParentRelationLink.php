<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Fields;

use Closure;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\UI\Components\ActionButton;

trait WithParentRelationLink
{
    protected Closure|bool $isParentRelationLink = false;

    protected ?string $parentRelationName = null;

    protected ?Closure $modifyParentRelationLinkButton = null;

    public function countLinkMode(?string $linkRelation = null, Closure|bool|null $condition = null): static
    {
        $this->parentRelationName = $linkRelation;

        if (is_null($condition)) {
            $this->isParentRelationLink = true;

            return $this;
        }

        $this->isParentRelationLink = $condition;

        return $this;
    }

    protected function isParentRelationLink(): bool
    {
        if (is_callable($this->isParentRelationLink) && is_null($this->toValue())) {
            return value($this->isParentRelationLink, 0, $this);
        }

        if (is_callable($this->isParentRelationLink)) {
            $count = $this->toValue() instanceof Collection
                ? $this->toValue()->count()
                : $this->toValue()->total();

            return value($this->isParentRelationLink, $count, $this);
        }

        return $this->isParentRelationLink;
    }

    protected function getParentRelationLinkButton(bool $preview = false): ActionButton
    {
        if (is_null($relationName = $this->parentRelationName)) {
            $relationName = str_replace('-resource', '', (string) moonshineRequest()->getResourceUri());
        }

        if (is_null($this->parentRelationName) && $this instanceof BelongsToMany) {
            $relationName = str($relationName)->plural();
        }

        $value = $this->toValue();
        $count = $value instanceof Paginator
            ? $value->total()
            : $value->count();

        return ActionButton::make(
            "($count)",
            $this->getResource()->indexPageUrl([
                '_parentId' => $relationName . '-' . $this->getRelatedModel()?->getKey(),
            ])
        )
            ->icon('eye')
            ->when(
                ! is_null($this->modifyParentRelationLinkButton),
                fn (ActionButton $button) => value($this->modifyParentRelationLinkButton, $button, preview: $preview)
            );
    }

    /**
     * @param  Closure(ActionButton $button, bool $preview, self $field): ActionButton  $callback
     */
    public function modifyParentRelationLinkButton(Closure $callback): self
    {
        $this->modifyParentRelationLinkButton = $callback;

        return $this;
    }
}

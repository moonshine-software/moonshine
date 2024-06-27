<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Fields;

use Closure;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\UI\Components\ActionButton;

trait WithRelatedLink
{
    protected Closure|bool $isRelatedLink = false;

    protected ?string $parentRelationName = null;

    protected ?Closure $modifyRelatedLink = null;

    public function relatedLink(?string $linkRelation = null, Closure|bool|null $condition = null): static
    {
        $this->parentRelationName = $linkRelation;

        if (is_null($condition)) {
            $this->isRelatedLink = true;

            return $this;
        }

        $this->isRelatedLink = $condition;

        return $this;
    }

    protected function isRelatedLink(): bool
    {
        if (is_callable($this->isRelatedLink) && is_null($this->toValue())) {
            return value($this->isRelatedLink, 0, $this);
        }

        if (is_callable($this->isRelatedLink)) {
            $count = $this->toValue() instanceof Collection
                ? $this->toValue()->count()
                : $this->toValue()->total();

            return value($this->isRelatedLink, $count, $this);
        }

        return $this->isRelatedLink;
    }

    protected function getRelatedLink(bool $preview = false): ActionButton
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
            '',
            url: $this->getResource()->getIndexPageUrl([
                '_parentId' => $relationName . '-' . $this->getRelatedModel()?->getKey(),
            ])
        )
            ->badge($count)
            ->icon('eye')
            ->when(
                ! is_null($this->modifyRelatedLink),
                fn (ActionButton $button) => value($this->modifyRelatedLink, $button, preview: $preview)
            );
    }

    /**
     * @param  Closure(ActionButton $button, bool $preview, self $field): ActionButton  $callback
     */
    public function modifyRelatedLink(Closure $callback): self
    {
        $this->modifyRelatedLink = $callback;

        return $this;
    }
}

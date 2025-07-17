<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Fields;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\UI\Components\ActionButton;

trait WithRelatedLink
{
    protected Closure|bool $isRelatedLink = false;

    protected ?string $parentRelationName = null;

    protected ?Closure $modifyRelatedLink = null;

    /**
     * @param  (Closure(int $count, static $ctx): bool)|bool|null  $condition
     */
    public function relatedLink(?string $linkRelation = null, Closure|bool|null $condition = null): static
    {
        $this->parentRelationName = $linkRelation;

        if (\is_null($condition)) {
            $this->isRelatedLink = true;

            return $this;
        }

        $this->isRelatedLink = $condition;

        return $this;
    }

    public function toRelatedCollection(): Collection
    {
        return $this->getRelatedModel()->{$this->getRelationName()} ?? new Collection();
    }

    protected function isRelatedLink(): bool
    {
        if ($this->isRelatedLink === false) {
            return false;
        }

        if (\is_callable($this->isRelatedLink)) {
            $value = $this->toRelatedCollection();
            $count = $value->count();

            return (bool) value($this->isRelatedLink, $count, $this);
        }

        return $this->isRelatedLink;
    }

    public function getRelatedLinkRelation(): string
    {
        if (! \is_null($this->parentRelationName)) {
            return $this->parentRelationName;
        }

        $resource = $this->getNowOnResource() ?? $this->getCore()->getCrudRequest()->getResource();

        $relationName = Str::of((string) $resource?->getUriKey())
            ->remove('-resource')
            ->replace('-', '_');

        if ($this instanceof BelongsToMany) {
            $relationName = $relationName->plural();
        }

        return (string) $relationName;
    }

    protected function getRelatedLink(bool $preview = false): ActionButtonContract
    {
        $relationName = $this->getRelatedLinkRelation();

        $value = $this->toRelatedCollection();
        $count = $value->count();

        return ActionButton::make(
            '',
            url: $this->getResource()->getIndexPageUrl([
                '_parentId' => $relationName . '-' . $this->getRelatedModel()?->getKey(),
            ]),
        )
            ->badge($count)
            ->icon('eye')
            ->when(
                ! \is_null($this->modifyRelatedLink),
                fn (ActionButtonContract $button) => value($this->modifyRelatedLink, $button, $preview),
            );
    }

    /**
     * @param  Closure(ActionButtonContract $button, bool $preview, static $ctx): ActionButtonContract  $callback
     */
    public function modifyRelatedLink(Closure $callback): static
    {
        $this->modifyRelatedLink = $callback;

        return $this;
    }
}

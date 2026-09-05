<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Fields;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\Support\Stringify;
use MoonShine\UI\Components\ActionButton;

trait WithRelatedLink
{
    protected Closure|bool $isRelatedLink = false;

    protected ?string $parentRelationName = null;

    /** @var null|(Closure(ActionButtonContract, bool, static): ActionButtonContract) */
    protected ?Closure $modifyRelatedLink = null;

    protected ?int $relatedCount = null;

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

    /** @return Collection<array-key, \Illuminate\Database\Eloquent\Model> */
    public function toRelatedCollection(): Collection
    {
        /** @var Collection<array-key, \Illuminate\Database\Eloquent\Model> */
        return $this->getRelatedModel()->{$this->getRelationName()} ?? new Collection();
    }

    protected function isRelatedLink(): bool
    {
        if ($this->isRelatedLink === false) {
            return false;
        }

        if ($this->relatedCount !== null) {
            return true;
        }

        $model = $this->getRelatedModel();

        if ($model === null) {
            return false;
        }

        if (\is_callable($this->isRelatedLink)) {
            $relation = $this->getRelationName();
            $this->relatedCount = $model->relationLoaded($relation)
                ? $this->toRelatedCollection()->count()
                : ($this->getRelation()?->count() ?? 0);

            $result = (bool) value($this->isRelatedLink, $this->relatedCount, $this);
            $this->relatedCount = $result === false ? null : $this->relatedCount;

            return $result;
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

        if ($this->relatedCount === null) {
            $model = $this->getRelatedModel();
            $relation = $this->getRelationName();

            $this->relatedCount = match (true) {
                ! $model || ! $relation => 0,
                $model->relationLoaded($relation) => $this->toRelatedCollection()->count(),
                default => ($this->getRelation()?->count() ?? 0),
            };
        }

        return ActionButton::make(
            '',
            url: $this->getResourceOrFail()->getIndexPageUrl([
                '_parentId' => $relationName . '-' . Stringify::value($this->getRelatedModel()?->getKey()),
            ]),
        )
            ->badge($this->relatedCount)
            ->icon('eye')
            ->when(
                ! \is_null($this->modifyRelatedLink),
                fn (ActionButtonContract $button) => value($this->modifyRelatedLink, $button, $preview, $this),
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

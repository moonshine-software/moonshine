<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Closure;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Fields\Relationships\BelongsToMany;

trait OnlyLink
{
    protected Closure|bool $onlyLink = false;

    protected bool $onlyLinkOnForm = false;

    protected ?string $linkRelation = null;

    protected ?Closure $modifyOnlyLinkButton = null;

    public function onlyLink(?string $linkRelation = null, Closure|bool|null $condition = null, bool $onForm = true): static
    {
        $this->linkRelation = $linkRelation;
        $this->onlyLinkOnForm = $onForm;

        if (is_null($condition)) {
            $this->onlyLink = true;

            return $this;
        }

        $this->onlyLink = $condition;

        return $this;
    }

    public function isOnlyLinkOnForm(): bool
    {
        return $this->onlyLinkOnForm;
    }

    public function isOnlyLink(): bool
    {
        if (is_callable($this->onlyLink) && is_null($this->toValue())) {
            return value($this->onlyLink, 0, $this);
        }

        if (is_callable($this->onlyLink)) {
            $count = $this->toValue() instanceof Collection
                ? $this->toValue()->count()
                : $this->toValue()->total();

            return value($this->onlyLink, $count, $this);
        }

        return $this->onlyLink;
    }

    public function getOnlyLinkRelation(): string
    {
        if (! is_null($this->linkRelation)) {
            return $this->linkRelation;
        }

        $relationName = str((string) moonshineRequest()->getResourceUri())
            ->remove('-resource')
            ->replace('-', '_');

        if ($this instanceof BelongsToMany) {
            $relationName = $relationName->plural();
        }

        return (string) $relationName;
    }

    protected function getOnlyLinkButton(bool $preview = false): ActionButton
    {
        $relationName = $this->getOnlyLinkRelation();

        $value = $this->toValue();
        $count = $value instanceof Paginator
            ? $value->total()
            : $value->count();

        return ActionButton::make(
            '',
            url: $this->getResource()->indexPageUrl([
                '_parentId' => $relationName . '-' . $this->getRelatedModel()?->getKey(),
            ])
        )
            ->badge($count)
            ->icon('heroicons.outline.eye')
            ->when(
                ! is_null($this->modifyOnlyLinkButton),
                fn (ActionButton $button) => value($this->modifyOnlyLinkButton, $button, preview: $preview)
            );
    }

    /**
     * @param  Closure(ActionButton $button, bool $preview, self $field): ActionButton  $callback
     */
    public function modifyOnlyLinkButton(Closure $callback): self
    {
        $this->modifyOnlyLinkButton = $callback;

        return $this;
    }
}

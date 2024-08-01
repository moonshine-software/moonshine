<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Buttons\BelongsToOrManyButton;
use MoonShine\Support\Condition;
use Closure;
use Throwable;

trait BelongsToOrManyCreatable
{
    protected bool $isCreatable = false;

    protected ?ActionButton $creatableButton = null;

    public function creatable(
        Closure|bool|null $condition = null,
        ?ActionButton $button = null,
    ): static {
        $this->isCreatable = Condition::boolean($condition, true);
        $this->creatableButton = $button;

        return $this;
    }

    public function isCreatable(): bool
    {
        return $this->isCreatable;
    }

    /**
     * @throws Throwable
     */
    public function createButton(): ?ActionButton
    {
        if (! $this->isCreatable()) {
            return null;
        }

        $button = BelongsToOrManyButton::for($this, button: $this->creatableButton);

        return $button->isSee($this->getRelatedModel())
            ? $button
            : null;
    }

    public function fragmentUrl(): string
    {
        return to_page(
            page: moonshineRequest()->getPage(),
            resource: moonshineRequest()->getResource(),
            params: ['resourceItem' => moonshineRequest()->getItemID()],
            fragment: $this->getRelationName()
        );
    }
}

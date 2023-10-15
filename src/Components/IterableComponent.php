<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Illuminate\Contracts\Pagination\Paginator;
use MoonShine\ActionButtons\ActionButtons;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Traits\HasDataCast;
use MoonShine\Traits\WithFields;

abstract class IterableComponent extends MoonshineComponent implements HasFields
{
    use HasDataCast;
    use WithFields;

    protected iterable $items = [];

    protected ?Paginator $paginator = null;

    protected array $buttons = [];

    public function items(iterable $items = []): static
    {
        if($items instanceof Paginator) {
            $this->items = $items->items();
            $this->paginator($items);
        } else {
            $this->items = $items;
        }

        return $this;
    }

    public function paginator(Paginator $paginator): static
    {
        $this->paginator = $paginator;

        return $this;
    }

    public function getPaginator(): ?Paginator
    {
        return $this->paginator;
    }

    public function hasPaginator(): bool
    {
        return ! is_null($this->paginator);
    }

    public function buttons(array $buttons = []): static
    {
        $this->buttons = $buttons;

        return $this;
    }

    public function getButtons(mixed $data): ActionButtons
    {
        return ActionButtons::make($this->buttons)
            ->filter()
            ->fillItem($this->castData($data))
            ->onlyVisible()
            ->withoutBulk();
    }
}

<?php

declare(strict_types=1);

namespace MoonShine\Crud\Concerns\Page;

use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Crud\Components\Fragment;
use MoonShine\Crud\Contracts\Page\IndexPageContract;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;

/**
 * @template TFields of \MoonShine\Crud\Collections\Fields
 * @mixin IndexPageContract
 */
trait HasListComponent
{
    public function getListComponentName(): string
    {
        return "index-table-{$this->getResourceOrFail()->getUriKey()}";
    }

    public function getListEventType(): JsEvent
    {
        return JsEvent::TABLE_UPDATED;
    }

    public function isListComponentRequest(): bool
    {
        return $this->getCore()->getRequest()->isAjax() && $this->getCore()->getRequest()->getScalar('_component_name') === $this->getListComponentName();
    }

    public function getListEventName(?string $name = null, array $params = []): string
    {
        $name ??= $this->getListComponentName();

        return AlpineJs::event($this->getListEventType(), $name, $params);
    }

    public function getListComponent(bool $withoutFragment = false): ComponentContract
    {
        $items = $this->isLazy() ? [] : $this->getResourceOrFail()->getItems();
        /** @var TFields $fields */
        $fields = $this->getResourceOrFail()->getIndexFields();

        $component = $this->getItemsComponent($items, $fields);

        if ($withoutFragment) {
            return $component;
        }

        return Fragment::make([$component])->name('crud-list');
    }
}

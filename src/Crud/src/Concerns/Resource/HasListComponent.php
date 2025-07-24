<?php

declare(strict_types=1);

namespace MoonShine\Crud\Concerns\Resource;

use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\HasListComponentContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;

/**
 * @mixin CrudResourceContract
 */
trait HasListComponent
{
    public function getListComponent(bool $withoutFragment = false): ?ComponentContract
    {
        $page = $this->getIndexPage();

        if (! $page instanceof HasListComponentContract) {
            return null;
        }

        return $page->getListComponent($withoutFragment);
    }

    public function getListComponentName(): string
    {
        $page = $this->getIndexPage();

        if (! $page instanceof HasListComponentContract) {
            return "index-table-{$this->getUriKey()}";
        }

        return $page->getListComponentName();
    }

    public function getListEventType(): JsEvent
    {
        return JsEvent::TABLE_UPDATED;
    }

    public function isListComponentRequest(): bool
    {
        if (! $this->getIndexPage() instanceof HasListComponentContract) {
            return false;
        }

        return $this->getIndexPage()->isListComponentRequest();
    }

    public function getListEventName(?string $name = null, array $params = []): string
    {
        $name ??= $this->getListComponentName();

        $page = $this->getIndexPage();

        if ($page instanceof HasListComponentContract) {
            return $page->getListEventName($name, $params);
        }

        return AlpineJs::event($this->getListEventType(), $name, $params);
    }
}

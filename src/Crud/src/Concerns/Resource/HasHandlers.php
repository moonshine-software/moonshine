<?php

declare(strict_types=1);

namespace MoonShine\Crud\Concerns\Resource;

use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Crud\Contracts\HasHandlersContract;
use MoonShine\Crud\Handlers\Handler;
use MoonShine\Crud\Handlers\Handlers;
use MoonShine\Crud\Pages\IndexPage;
use MoonShine\Support\ListOf;

/**
 * @mixin CrudResourceContract
 * @deprecated Will be removed in 5.0
 * @see IndexPage
 */
trait HasHandlers
{
    /**
     * @return ListOf<Handler>
     */
    protected function handlers(): ListOf
    {
        return new ListOf(Handler::class, []);
    }

    public function hasHandlers(): bool
    {
        if ($this->handlers()->toArray() !== []) {
            return true;
        }

        return $this->getIndexPage() instanceof HasHandlersContract && $this->getIndexPage()->hasHandlers();
    }

    public function getHandlers(): Handlers
    {
        $handlers = $this->handlers()->toArray();

        if ($this->getIndexPage() instanceof HasHandlersContract && $this->getIndexPage()->hasHandlers()) {
            $handlers = array_merge($handlers, $this->getIndexPage()->getHandlers()->toArray());
        }

        return Handlers::make($handlers)
            ->each(fn (Handler $handler): Handler => $handler->setResource($this));
    }
}

<?php

declare(strict_types=1);

namespace MoonShine\Crud\Concerns\Page;

use MoonShine\Crud\Contracts\Page\IndexPageContract;
use MoonShine\Crud\Handlers\Handler;
use MoonShine\Crud\Handlers\Handlers;
use MoonShine\Support\ListOf;

/**
 * @mixin IndexPageContract
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
        return $this->handlers()->toArray() !== [];
    }

    public function getHandlers(): Handlers
    {
        return Handlers::make($this->handlers()->toArray())
            ->each(fn (Handler $handler): Handler => $handler->setResource($this->getResource()));
    }
}

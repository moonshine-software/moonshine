<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

use MoonShine\Laravel\Enums\Action;
use MoonShine\Laravel\Handlers\Handler;
use MoonShine\Laravel\Handlers\Handlers;
use MoonShine\Support\ListOf;

trait ResourceModelActions
{
    /**
     * @return ListOf<Action>
     */
    public function activeActions(): ListOf
    {
        return new ListOf(Action::class, [
            Action::CREATE,
            Action::VIEW,
            Action::UPDATE,
            Action::DELETE,
            Action::MASS_DELETE,
        ]);
    }

    /**
     * @return list<Action>
     */
    protected function getActiveActions(): array
    {
        return $this->activeActions()->toArray();
    }

    public function hasAction(Action ...$actions): bool
    {
        return collect($actions)->every(fn (Action $action): bool => in_array($action, $this->getActiveActions()));
    }

    /**
     * @return ListOf<Handler>
     */
    protected function handlers(): ListOf
    {
        return new ListOf(Handler::class, []);
    }

    public function getHandlers(): Handlers
    {
        return Handlers::make($this->handlers()->toArray())
            ->each(fn (Handler $handler): Handler => $handler->setResource($this));
    }
}

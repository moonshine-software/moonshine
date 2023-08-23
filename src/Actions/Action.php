<?php

declare(strict_types=1);

namespace MoonShine\Actions;

use Closure;
use MoonShine\Contracts\Actions\ActionContract;
use MoonShine\Contracts\HasResourceContract;
use MoonShine\Exceptions\ActionException;
use MoonShine\Traits\HasResource;
use Throwable;

/**
 * @method static static make(Closure|string $label = '')
 */
abstract class Action extends AbstractAction implements ActionContract, HasResourceContract
{
    use HasResource;

    protected ?string $triggerKey = null;

    protected bool $withQuery = false;

    final public function __construct(Closure|string $label = '')
    {
        $this->setLabel($label);
    }

    abstract public function handle(): mixed;

    /**
     * @throws ActionException|Throwable
     */
    public function url(): string
    {
        if (! $this->hasResource()) {
            throw new ActionException('Resource is required for action');
        }

        $query = [$this->getTriggerKey() => true];

        if ($this->withQuery()) {
            $query = array_merge(request()->query(), $query);
        }

        return $this->getResource()
            ->route('actions', query: $query);
    }

    public function getTriggerKey(): string
    {
        return $this->triggerKey ?? class_basename($this);
    }

    protected function withQuery(): bool
    {
        return $this->withQuery;
    }

    public function isTriggered(): bool
    {
        return request()->has($this->getTriggerKey());
    }
}

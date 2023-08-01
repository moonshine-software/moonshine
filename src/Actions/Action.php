<?php

declare(strict_types=1);

namespace MoonShine\Actions;

use MoonShine\Contracts\Actions\ActionContract;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Exceptions\ActionException;

/**
 * @method static static make(string $label = '')
 */
abstract class Action extends AbstractAction implements ActionContract
{
    protected ?ResourceContract $resource = null;

    protected ?string $triggerKey = null;

    protected bool $withQuery = false;

    final public function __construct(string $label = '')
    {
        $this->setLabel($label);
    }

    abstract public function handle(): mixed;

    /**
     * @throws ActionException
     */
    public function url(): string
    {
        if (is_null($this->resource())) {
            throw new ActionException('Resource is required for action');
        }

        $query = [$this->getTriggerKey() => true];

        if ($this->withQuery()) {
            $query = array_merge(request()->query(), $query);
        }

        return $this->resource()
            ->route('actions.index', query: $query);
    }

    public function resource(): ?ResourceContract
    {
        return $this->resource;
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

    public function setResource(ResourceContract $resource): static
    {
        $this->resource = $resource;

        return $this;
    }
}

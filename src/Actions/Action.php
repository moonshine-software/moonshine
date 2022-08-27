<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Actions;

use JsonSerializable;
use Leeto\MoonShine\Exceptions\ActionException;
use Leeto\MoonShine\Resources\Resource;
use Leeto\MoonShine\Traits\Makeable;

abstract class Action implements JsonSerializable
{
    use Makeable;

    protected string $label;

    protected ?Resource $resource;

    final public function __construct(string $label)
    {
        $this->setLabel($label);
    }

    abstract public function handle(): mixed;

    abstract public function url(): string;

    abstract public function isTriggered(): bool;

    public function label(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function resource(): ?Resource
    {
        return $this->resource;
    }

    public function setResource(Resource $resource): static
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @throws ActionException
     */
    public function jsonSerialize(): array
    {
        return [
            'label' => $this->label(),
            'url' => $this->url(),
        ];
    }
}

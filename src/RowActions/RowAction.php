<?php

declare(strict_types=1);

namespace Leeto\MoonShine\RowActions;

use Closure;
use JsonSerializable;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithComponentAttributes;
use Leeto\MoonShine\Traits\WithIcon;

abstract class RowAction implements JsonSerializable
{
    use Makeable;
    use WithIcon;
    use WithComponentAttributes;

    protected string $route;

    final public function __construct(
        protected string $title,
        protected ?Closure $callback = null
    ) {
    }

    abstract public function resolveRoute(string $routeParam, string|int $primaryKey): static;

    public function route(): string
    {
        return $this->route;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function callback(...$arguments): mixed
    {
        return is_callable($this->callback)
            ? call_user_func($this->callback, ...$arguments)
            : null;
    }

    public function type(): string
    {
        return str(static::class)
            ->snake()
            ->value();
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type(),
            'title' => $this->title(),
            'icon' => $this->getIcon(),
            'route' => $this->route(),
            'attributes' => $this->attributes()->getAttributes(),
        ];
    }
}

<?php

declare(strict_types=1);

namespace Leeto\MoonShine\ValueEntities;

use Leeto\MoonShine\Contracts\ValueEntityContract;
use Leeto\MoonShine\RowActions\RowAction;
use Leeto\MoonShine\Traits\Makeable;

final class ModelValueEntity implements ValueEntityContract
{
    use Makeable;

    /**
     * @var array<RowAction>
     */
    protected array $actions = [];

    public function __construct(
        protected string $primaryKeyName = 'id',
        protected ?string $foreignKeyName = null,
        protected array $attributes = []
    ) {
    }

    /**
     * @return string
     */
    public function primaryKeyName(): string
    {
        return $this->primaryKeyName;
    }

    /**
     * @return string|null
     */
    public function foreignKeyName(): ?string
    {
        return $this->foreignKeyName;
    }

    /**
     * @param  string|null  $key
     * @return mixed
     */
    public function attributes(string $key = null): mixed
    {
        return $key ? $this->attributes[$key] : $this->attributes;
    }

    public function withActions(array $actions): ModelValueEntity
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * @return array<RowAction>
     */
    public function actions(): array
    {
        return $this->actions;
    }

    public function primaryKey(): mixed
    {
        return $this->attributes($this->primaryKeyName());
    }

    public function __get(string $name)
    {
        return $this->attributes($name);
    }

    public function id(): int
    {
        return $this->primaryKey();
    }
}

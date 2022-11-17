<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Closure;
use Illuminate\Support\Traits\Macroable;
use JsonSerializable;
use Leeto\MoonShine\Contracts\Fields\HasAssets;
use Leeto\MoonShine\Contracts\Fields\HasFields;
use Leeto\MoonShine\Contracts\Fields\HasRelationship;
use Leeto\MoonShine\Contracts\Resources\ResourceContract;
use Leeto\MoonShine\Contracts\ValueEntityContract;
use Leeto\MoonShine\Helpers\Condition;
use Leeto\MoonShine\MoonShine;
use Leeto\MoonShine\Traits\Fields\HintTrait;
use Leeto\MoonShine\Traits\Fields\LinkTrait;
use Leeto\MoonShine\Traits\Fields\ShowOrHide;
use Leeto\MoonShine\Traits\Fields\ShowWhen;
use Leeto\MoonShine\Traits\Fields\WithHtmlAttributes;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithAssets;
use Leeto\MoonShine\Traits\WithComponent;
use Leeto\MoonShine\Traits\WithComponentAttributes;
use Leeto\MoonShine\Utilities\AssetManager;

abstract class Field implements JsonSerializable, HasAssets
{
    use Makeable;
    use Macroable;
    use WithHtmlAttributes;
    use WithComponentAttributes;
    use WithComponent;
    use ShowOrHide;
    use ShowWhen;
    use LinkTrait;
    use HintTrait;
    use WithAssets;

    /**
     * table column/model attribute
     */
    protected string $column;

    protected string $label;

    protected ?string $relation = null;

    protected ?ResourceContract $resource;

    protected string $resourceColumn = 'id';

    protected mixed $value = null;

    protected ?Closure $valueCallback = null;

    protected ?string $default = null;

    protected bool $nullable = false;

    protected bool $sortable = false;

    final public function __construct(
        string $label = null,
        string $column = null,
        Closure|ResourceContract|string|null $resource = null
    ) {
        $this->resolveLabel($label);
        $this->resolveColumn($column);
        $this->resolveRelation();
        $this->resolveResource($resource);
        $this->resolveValueCallback($resource);
    }

    protected function afterMake(): void
    {
        if ($this->getAssets()) {
            AssetManager::add($this->getAssets());
        }
    }

    protected function resolveLabel(string $label = null): void
    {
        $this->label = $label ?? (string) str($this->label)->ucfirst();
    }

    protected function resolveColumn(string $column = null): void
    {
        $this->column = $column ?? (string) str($this->label)->lower()->snake();

        if ($this->hasRelationship()) {
            $this->column = $column ?? (string) str($this->label)->camel();

            if ($this instanceof BelongsTo && !str($this->column)->contains('_id')) {
                $this->column = (string) str($this->column)
                    ->append('_id')
                    ->snake();
            }
        }
    }

    protected function resolveRelation(): void
    {
        if ($this->hasRelationship()) {
            $this->relation = $this->column ?? (string) str($this->label)->camel();

            if (str($this->relation)->contains('_id')) {
                $this->relation = (string) str($this->relation)
                    ->remove('_id')
                    ->camel();
            }
        }
    }

    protected function resolveResource(Closure|ResourceContract|string|null $argument): void
    {
        if ($argument instanceof ResourceContract) {
            $this->resource = $argument;
        } elseif (is_string($argument)) {
            $this->resourceColumn = $argument;
        } elseif ($this->hasRelationship()) {
            $this->resource = $this->findResource();
        }
    }

    protected function resolveValueCallback(Closure|ResourceContract|string|null $argument): void
    {
        if ($argument instanceof Closure) {
            $this->valueCallback = $argument;
        }
    }

    public function label(): string
    {
        return $this->label;
    }

    public function column(): string
    {
        return $this->column;
    }

    public function relation(): ?string
    {
        return $this->relation;
    }

    public function resource(): ?ResourceContract
    {
        return $this->resource ?? $this->findResource();
    }

    protected function findResource(): ?ResourceContract
    {
        $resourceClass = (string) str(MoonShine::namespace('\Resources\\'))
            ->append(str($this->relation() ?? $this->column())->studly()->singular())
            ->append('Resource');

        return class_exists($resourceClass) ? new $resourceClass() : null;
    }

    public function resourceColumn(): string
    {
        return $this->resource()
            ? $this->resource()->column()
            : $this->resourceColumn;
    }

    public function valueCallback(): ?Closure
    {
        return $this->valueCallback;
    }

    public function hasRelationship(): bool
    {
        return $this instanceof HasRelationship;
    }

    public function default(string $default): static
    {
        $this->default = $default;

        return $this;
    }

    public function getDefault(): ?string
    {
        return old($this->nameDot(), $this->default);
    }

    public function setValue(mixed $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function resolveFill(ValueEntityContract $values): static
    {
        $this->setValue(
            $values->attributes($this->relation() ?? $this->column()) ?? null
        );

        return $this;
    }

    public function value(): mixed
    {
        if (is_callable($this->valueCallback())) {
            return $this->valueCallback()($this->value);
        }

        return $this->value ?? $this->getDefault();
    }

    public function requestValue(string $prefix = null): mixed
    {
        return request(
            $this->nameDot($prefix),
            $this->getDefault() ?? old($this->nameDot($prefix), false)
        );
    }

    public function nullable($condition = null): static
    {
        $this->nullable = Condition::boolean($condition, true);

        return $this;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * Define whether if index page can be sorted by this field
     *
     * @return $this
     */
    public function sortable(): static
    {
        $this->sortable = true;

        return $this;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function jsonSerialize(): array
    {
        return [
            'component' => $this->getComponent(),
            'id' => $this->id(),
            'name' => $this->name(),
            'label' => $this->label(),
            'key' => $this->column(),
            'relation' => $this->relation(),
            'value' => $this->value(),
            'resource' => $this->resource()?->uriKey(),
            'attributes' => $this->attributes()->getAttributes(),
            'fields' => $this instanceof HasFields ? $this->getFields() : []
        ];
    }
}

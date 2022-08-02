<?php

declare(strict_types=1);

namespace Leeto\MoonShine;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use JsonSerializable;
use Leeto\MoonShine\Contracts\Fields\HasFields;
use Leeto\MoonShine\Contracts\Fields\Relationships\BelongsToRelation;
use Leeto\MoonShine\Contracts\Fields\Relationships\HasRelationship;
use Leeto\MoonShine\Contracts\Fields\Relationships\ManyToManyRelation;
use Leeto\MoonShine\Contracts\Fields\Relationships\OneToManyRelation;
use Leeto\MoonShine\Contracts\Fields\Relationships\OneToOneRelation;
use Leeto\MoonShine\Contracts\Renderable;
use Leeto\MoonShine\Resources\Resource;
use Leeto\MoonShine\Traits\Fields\WithHtmlAttributes;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithComponentAttributes;
use Leeto\MoonShine\Traits\WithView;
use Stringable;

abstract class FormElement implements Renderable, JsonSerializable, Stringable
{
    use Makeable, WithHtmlAttributes, WithComponentAttributes, WithView;

    protected string $label = '';

    protected string $field;

    protected ?string $relation = null;

    protected ?Resource $resource;

    protected bool $group = false;

    protected string $resourceTitleField = '';

    protected ?Closure $valueCallback = null;

    protected mixed $value = null;

    protected static string $view = '';

    final public function __construct(
        string $label = null,
        string $field = null,
        Closure|Resource|string|null $resource = null
    ) {
        $this->setLabel($label ?? (string) str($this->label)->ucfirst());
        $this->setField($field ?? (string) str($this->label)->lower()->snake());

        if ($this->hasRelationship()) {
            $this->setField($field ?? (string) str($this->label)->camel());

            if ($this->belongToOne() && !str($this->field())->contains('_id')) {
                $this->setField(
                    (string) str($this->field())
                        ->append('_id')
                        ->snake()
                );
            }

            $this->setRelation($field ?? (string) str($this->label)->camel());

            if (str($this->relation())->contains('_id')) {
                $this->setRelation(
                    (string) str($this->relation())
                        ->remove('_id')
                        ->camel()
                );
            }

            if ($resource instanceof Resource) {
                $this->setResource($resource);
            } elseif (is_string($resource)) {
                $this->setResourceTitleField($resource);
            }
        }

        if ($resource instanceof Closure) {
            $this->setValueCallback($resource);
        }
    }

    public function label(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function field(): string
    {
        return $this->field;
    }

    public function setField(string $field): static
    {
        $this->field = $field;

        return $this;
    }

    public function relation(): ?string
    {
        return $this->relation;
    }

    public function setRelation(string $relation): static
    {
        $this->relation = $relation;

        return $this;
    }

    public function resource(): ?Resource
    {
        return $this->resource ?? $this->findResource();
    }

    protected function findResource(): ?Resource
    {
        $resourceClass = (string) str(MoonShine::namespace('\Resources\\'))
            ->append(str($this->relation() ?? $this->field())->studly()->singular())
            ->append('Resource');

        return class_exists($resourceClass) ? new $resourceClass() : null;
    }

    public function setResource(?Resource $resource): void
    {
        $this->resource = $resource;
    }

    public function resourceTitleField(): string
    {
        if ($this->resourceTitleField) {
            return $this->resourceTitleField;
        }
        return $this->resource() && $this->resource()->titleField()
            ? $this->resource()->titleField()
            : 'id';
    }

    public function setResourceTitleField(string $resourceTitleField): static
    {
        $this->resourceTitleField = $resourceTitleField;

        return $this;
    }

    public function valueCallback(): ?Closure
    {
        return $this->valueCallback;
    }

    protected function setValueCallback(Closure $valueCallback): void
    {
        $this->valueCallback = $valueCallback;
    }

    public function getRelated(Model $model): Model
    {
        return $model->{$this->relation()}()->getRelated();
    }

    protected function group(): static
    {
        $this->group = true;

        return $this;
    }

    protected function isGroup(): bool
    {
        return $this->group;
    }

    public function hasRelationship(): bool
    {
        return $this instanceof HasRelationship;
    }

    public function belongToOne(): bool
    {
        return $this->hasRelationship() && $this instanceof BelongsToRelation;
    }

    public function toOne(): bool
    {
        return $this->hasRelationship() && $this instanceof OneToOneRelation;
    }

    public function toMany(): bool
    {
        return $this->hasRelationship() && $this instanceof OneToManyRelation;
    }

    public function manyToMany(): bool
    {
        return $this->hasRelationship() && $this instanceof ManyToManyRelation;
    }

    public function setValue(mixed $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function value(): mixed
    {
        return $this->value;
    }

    public function requestValue(): mixed
    {
        return request(
            $this->nameDot(),
            $this->getDefault() ?? old($this->nameDot(), false)
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'attributes' => $this->attributes(),
            'element' => $this,
            'fields' => $this instanceof HasFields ? $this->getFields() : []
        ];
    }

    public function render(): View
    {
        return view($this->getView(), [
            'element' => $this,
        ]);
    }

    public function __toString()
    {
        return (string) $this->render();
    }
}

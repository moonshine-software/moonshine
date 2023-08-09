<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use MoonShine\Casts\ModelCast;
use MoonShine\Contracts\HasResourceContract;
use MoonShine\Fields\Field;
use MoonShine\Resources\ModelResource;
use MoonShine\Traits\HasResource;

/**
 * @method static static make(string $label, string $relation, ModelResource $resource, ?Closure $valueCallback = null)
 */
abstract class ModelRelationField extends Field implements HasResourceContract
{
    use HasResource;

    protected ?Model $relatedModel = null;

    protected bool $outsideComponent = false;

    protected bool $toOne = false;

    public function __construct(
        string $label,
        protected string $relation,
        ModelResource $resource,
        ?Closure $valueCallback = null
    ) {
        parent::__construct();

        $this->setColumn($relation);
        $this->setLabel($label);
        $this->setResource($resource);

        if (! is_null($valueCallback)) {
            $this->setValueCallback($valueCallback);
        }
    }

    protected function prepareFill(array $raw = [], mixed $casted = null): mixed
    {
        return $casted?->{$this->getRelationName()};
    }

    public function resolveFill(array $raw = [], mixed $casted = null): Field
    {
        if ($this->value) {
            return $this;
        }

        $this->setRelatedModel($casted);

        $data = $this->prepareFill($raw, $casted);

        $this->setValue($data);

        if ($this->toOne()) {
            $this->setColumn(
                $this->getRelation()?->getForeignKeyName() ?? ''
            );

            $this->setRawValue(
                $raw[$this->column()] ?? null
            );

            $this->setFormattedValue(
                $data?->{$this->getResource()->column()}
            );

            if (is_callable($this->valueCallback())) {
                $this->setFormattedValue(
                    call_user_func(
                        $this->valueCallback(),
                        $data
                    )
                );
            }
        } else {
            $this->setColumn(
                $this->getRelationName()
            );
        }

        return $this;
    }

    protected function afterMake(): void
    {
        if ($this->getAssets()) {
            moonshineAssets()->add($this->getAssets());
        }
    }

    public function outsideComponent(): bool
    {
        return $this->outsideComponent;
    }

    public function toOne(): bool
    {
        return $this->toOne;
    }

    public function getRelationName(): string
    {
        return $this->relation;
    }

    public function setRelatedModel(?Model $model = null): void
    {
        $this->relatedModel = $model;
    }

    public function getModelCast(): ModelCast
    {
        return ModelCast::make($this->getRelation()?->getRelated()::class);
    }

    public function getRelatedModel(): ?Model
    {
        return $this->relatedModel;
    }

    public function getRelation(): ?Relation
    {
        return $this->getRelatedModel()
            ?->{$this->getRelationName()}();
    }
}

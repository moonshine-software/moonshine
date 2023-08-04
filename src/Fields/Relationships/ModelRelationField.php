<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use MoonShine\Contracts\HasResourceContract;
use MoonShine\Fields\Field;
use MoonShine\Resources\ModelResource;
use MoonShine\Traits\HasResource;
use MoonShine\Utilities\AssetManager;

/**
 * @method static make(string $label, string $relation, ModelResource $resource, ?Closure $valueCallback = null)
 */
abstract class ModelRelationField extends Field implements HasResourceContract
{
    use HasResource;

    protected ?Model $relatedModel = null;

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

    public function resolveFill(array $rawValues = [], mixed $castedValues = null): Field
    {
        if ($this->value) {
            return $this;
        }

        $this->setRelatedModel($castedValues);

        $data = $castedValues->{$this->getRelationName()};
        $relation = $castedValues->{$this->getRelationName()}();

        if ($this instanceof BelongsTo) {
            $this->setColumn(
                $relation->getForeignKeyName()
            );

            $this->setRawValue(
                $rawValues[$this->column()] ?? null
            );
        } else {
            $this->setColumn(
                $this->getRelationName()
            );
        }

        $this->setValue($data);

        if ($this instanceof BelongsTo) {
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
        }

        return $this;
    }

    protected function afterMake(): void
    {
        if ($this->getAssets()) {
            (new AssetManager())->add($this->getAssets());
        }
    }

    public function getRelationName(): string
    {
        return $this->relation;
    }

    public function setRelatedModel(Model $model): void
    {
        $this->relatedModel = $model;
    }

    public function getRelatedModel(): ?Model
    {
        return $this->relatedModel;
    }

    public function getRelation(): Relation
    {
        return $this->getRelatedModel()
            ?->{$this->getRelationName()}();
    }
}

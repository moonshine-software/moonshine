<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Stringable;
use MoonShine\Casts\ModelCast;
use MoonShine\Contracts\HasResourceContract;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Fields\Field;
use MoonShine\MoonShine;
use MoonShine\Resources\ModelResource;
use MoonShine\Traits\HasResource;
use Throwable;

/**
 * @method static static make(string $label, string $relation, ModelResource $resource, ?Closure $valueCallback = null)
 */
abstract class ModelRelationField extends Field implements HasResourceContract
{
    use HasResource;

    protected string $relationName;

    protected ?Model $relatedModel = null;

    protected bool $outsideComponent = false;

    protected bool $toOne = false;

    public function __construct(
        string $label,
        ?string $relationName = null,
        ?ModelResource $resource = null,
        ?Closure $valueCallback = null
    ) {
        parent::__construct($label, $relationName, $valueCallback);

        if (is_null($relationName)) {
            $relationName = str($label)
                ->camel()
                ->when(
                    $this->toOne(),
                    fn (Stringable $str) => $str->singular(),
                    fn (Stringable $str) => $str->plural(),
                )->value();
        }

        $this->setRelationName($relationName);

        if (is_null($resource)) {
            $resource = $this->findResource();
        }

        $this->setResource($resource);
    }

    /**
     * @throws Throwable
     */
    protected function findResource(): ResourceContract
    {
        if ($this->hasResource()) {
            return $this->getResource();
        }

        return MoonShine::getResourceFromUriKey(
            str($this->getRelationName())
                ->singular()
                ->append('Resource')
                ->kebab()
                ->value()
        );
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

    public function setRelationName(string $relationName): void
    {
        $this->relationName = $relationName;
    }

    public function getRelationName(): string
    {
        return $this->relationName;
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

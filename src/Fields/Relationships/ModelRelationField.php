<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Stringable;
use MoonShine\Contracts\HasResourceContract;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Fields\Field;
use MoonShine\MoonShine;
use MoonShine\Resources\ModelResource;
use MoonShine\Traits\HasResource;
use MoonShine\TypeCasts\ModelCast;
use Throwable;

/**
 * @method static static make(Closure|string $label, ?string $relationName = null, ?ModelResource $resource = null, ?Closure $formattedValueCallback = null)
 */
abstract class ModelRelationField extends Field implements HasResourceContract
{
    use HasResource;

    protected string $relationName;

    protected ?Model $relatedModel = null;

    protected bool $outsideComponent = false;

    protected bool $toOne = false;

    public function __construct(
        Closure|string $label,
        ?string $relationName = null,
        Closure|string|null $formattedValueCallback = null,
        ?ModelResource $resource = null,
    ) {
        if(is_string($formattedValueCallback)) {
            $formattedValueCallback = fn($item) => $item->{$formattedValueCallback};
        }

        parent::__construct($label, $relationName, $formattedValueCallback);

        if (is_null($relationName)) {
            $relationName = str($this->label())
                ->camel()
                ->when(
                    $this->toOne(),
                    fn (Stringable $str): Stringable => $str->singular(),
                    fn (Stringable $str): Stringable => $str->plural(),
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

    public function resolveFill(array $raw = [], mixed $casted = null, int $index = 0): Field
    {
        $this->setRelatedModel($casted);

        if ($this->value) {
            return $this;
        }

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
                $data?->{$this->getResourceColumn()}
            );

            if (is_closure($this->formattedValueCallback())) {
                $this->setFormattedValue(
                    call_user_func(
                        $this->formattedValueCallback(),
                        $data ?? $this->getRelation()?->getModel()
                    )
                );
            }
        }

        return $this;
    }

    public function outsideComponent(): bool
    {
        return $this->outsideComponent;
    }

    public function toOne(): bool
    {
        return $this->toOne;
    }

    protected function setRelationName(string $relationName): void
    {
        $this->relationName = $relationName;
    }

    public function getRelationName(): string
    {
        return $this->relationName;
    }

    protected function setRelatedModel(?Model $model = null): void
    {
        $this->relatedModel = $model;
    }

    protected function getModelCast(): ModelCast
    {
        return ModelCast::make($this->getRelation()?->getRelated()::class);
    }

    /**
     * @throws Throwable
     */
    public function getResourceColumn(): string
    {
        return $this->getResource()->column() ?? 'id';
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

<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Stringable;
use MoonShine\Contracts\HasResourceContract;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Exceptions\FieldException;
use MoonShine\Fields\Field;
use MoonShine\Resources\ModelResource;
use MoonShine\Traits\HasResource;
use MoonShine\TypeCasts\ModelCast;
use Throwable;

/**
 * @template-covariant R of Relation
 * @method static static make(Closure|string $label, ?string $relationName = null, Closure|string|null $formatted = null, ?ModelResource $resource = null)
 */
abstract class ModelRelationField extends Field implements HasResourceContract
{
    /** @use HasResource<ModelResource, ModelResource> */
    use HasResource;

    protected string $relationName;

    protected ?Model $relatedModel = null;

    protected bool $outsideComponent = false;

    protected bool $toOne = false;

    protected bool $isMorph = false;

    /**
     * @throws Throwable
     */
    public function __construct(
        Closure|string $label,
        ?string $relationName = null,
        Closure|string|null $formatted = null,
        ?ModelResource $resource = null,
    ) {
        if (is_string($formatted)) {
            $formatted = static fn ($item) => data_get($item, $formatted);
        }

        parent::__construct($label, $relationName, $formatted);

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

        if ($this->toOne() && ! $this->outsideComponent()) {
            $this->setColumn(
                str($this->getRelationName())
                    ->singular()
                    ->snake()
                    ->append('_id')
                    ->value()
            );
        }

        if (is_null($resource)) {
            $this->setResource($this->findResource());
        } else {
            $this->setResource($resource);
        }
    }

    /**
     * @throws Throwable
     */
    protected function findResource(): ResourceContract
    {
        if ($this->hasResource()) {
            return $this->getResource();
        }

        $resource = moonshine()->getResourceFromUriKey(
            str($this->getRelationName())
                ->singular()
                ->append('Resource')
                ->kebab()
                ->value()
        );

        if (is_null($resource) && $this->isMorph()) {
            $resource = moonshine()->getResourceFromUriKey(
                moonshineRequest()->getResourceUri()
            );
        }

        return tap(
            $resource,
            function (?ResourceContract $resource): void {
                throw_if(
                    is_null($resource),
                    FieldException::resourceRequired(static::class, $this->getRelationName())
                );
            }
        );
    }

    protected function prepareFill(array $raw = [], mixed $casted = null): mixed
    {
        return data_get($casted ?? $raw, $this->getRelationName());
    }

    /**
     * @throws Throwable
     */
    public function resolveFill(array $raw = [], mixed $casted = null, int $index = 0): static
    {
        if ($casted instanceof Model) {
            $this->setRelatedModel($casted);
        }

        $this->setData($raw ?? $casted);

        $data = $this->prepareFill($raw, $casted);

        $this->setValue($data);
        $this->setRowIndex($index);

        if ($this->toOne()) {
            $this->setColumn(
                $this->getRelation()?->getForeignKeyName() ?? ''
            );

            $this->setRawValue(
                $raw[$this->column()] ?? null
            );

            $this->setFormattedValue(
                data_get($data, $this->getResourceColumn())
            );
        }

        if(! is_null($this->afterFillCallback)) {
            return value($this->afterFillCallback, $this);
        }

        return $this;
    }

    public function toFormattedValue(): mixed
    {
        $value = $this->toValue(withDefault: false);

        if ($this->toOne() && is_closure($this->formattedValueCallback())) {
            $this->setFormattedValue(
                value(
                    $this->formattedValueCallback(),
                    $value ?? $this->getRelation()?->getModel(),
                    $this->getRowIndex()
                )
            );
        }

        if ($this->toOne()) {
            $value = data_get($value, $this->getResource()?->column());
        }

        return $this->formattedValue ?? $value;
    }

    public function outsideComponent(): bool
    {
        return $this->outsideComponent;
    }

    public function toOne(): bool
    {
        return $this->toOne;
    }

    public function isMorph(): bool
    {
        return $this->isMorph;
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
        return $this->getResource()?->column() ?? 'id';
    }

    public function getRelatedModel(): ?Model
    {
        return $this->relatedModel;
    }

    /** @return R */
    public function getRelation(): ?Relation
    {
        return $this->getRelatedModel()
            ?->{$this->getRelationName()}();
    }

    protected function onChangeCondition(): bool
    {
        return ! $this->outsideComponent();
    }
}

<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Fields\Relationships;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Stringable;
use MoonShine\Core\Contracts\CastedData;
use MoonShine\Core\Contracts\HasResourceContract;
use MoonShine\Core\Contracts\ResourceContract;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Traits\HasResource;
use MoonShine\UI\Exceptions\FieldException;
use MoonShine\UI\Fields\Field;
use Throwable;

/**
 * @template-covariant R of Relation
 * @method static static make(Closure|string $label, ?string $relationName = null, Closure|string|null $formatted = null, string|ModelResource|null $resource = null)
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
        ModelResource|string|null $resource = null,
    ) {
        if (is_string($formatted)) {
            $formatted = static fn ($item) => data_get($item, $formatted);
        }

        parent::__construct($label, $relationName, $formatted);

        if (is_null($relationName)) {
            $relationName = str($this->getLabel())
                ->camel()
                ->when(
                    $this->isToOne(),
                    static fn (Stringable $str): Stringable => $str->singular(),
                    static fn (Stringable $str): Stringable => $str->plural(),
                )->value();
        }

        $this->setRelationName($relationName);

        if ($this->isToOne() && ! $this->isOutsideComponent()) {
            $this->setColumn(
                str($this->getRelationName())
                    ->singular()
                    ->snake()
                    ->append('_id')
                    ->value()
            );
        }

        if (is_string($resource)) {
            $this->setResource($this->findResource($resource));
        } elseif (is_null($resource)) {
            $this->setResource($this->findResource());
        } else {
            $this->setResource($resource);
        }
    }

    /**
     * @param  ?class-string<ResourceContract>  $classString
     * @throws Throwable
     */
    protected function findResource(?string $classString = null): ResourceContract
    {
        if ($this->hasResource()) {
            return $this->getResource();
        }

        $resource = $classString
            ? moonshine()->getResources()->findByClass($classString)
            : moonshine()->getResources()->findByUri(
                str($this->getRelationName())
                    ->singular()
                    ->append('Resource')
                    ->kebab()
                    ->value()
            );

        if (is_null($resource) && $this->isMorph()) {
            $resource = moonshine()->getResources()->findByUri(
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

    protected function prepareFill(array $raw = [], ?CastedData $casted = null): mixed
    {
        return $casted?->getOriginal()?->{$this->getRelationName()};
    }

    /**
     * @throws Throwable
     */
    protected function resolveFill(array $raw = [], ?CastedData $casted = null, int $index = 0): static
    {
        if ($casted?->getOriginal() instanceof Model) {
            $this->setRelatedModel($casted?->getOriginal());
        }

        $this->setData($casted);

        $data = $this->prepareFill($raw, $casted);

        $this->setValue($data);
        $this->setRowIndex($index);

        if ($this->isToOne()) {
            $this->setColumn(
                $this->getRelation()?->getForeignKeyName() ?? ''
            );

            $this->setRawValue(
                $raw[$this->getColumn()] ?? null
            );

            $this->setFormattedValue(
                data_get($data, $this->getResourceColumn())
            );
        }

        if (! is_null($this->afterFillCallback)) {
            return value($this->afterFillCallback, $this);
        }

        return $this;
    }

    public function toFormattedValue(): mixed
    {
        $value = $this->toValue(withDefault: false);

        if ($this->isToOne() && ! is_null($this->getFormattedValueCallback())) {
            $this->setFormattedValue(
                value(
                    $this->getFormattedValueCallback(),
                    $value ?? $this->getRelation()?->getModel(),
                    $this->getRowIndex()
                )
            );
        }

        if ($this->isToOne()) {
            $value = data_get($value, $this->getResource()?->getColumn());
        }

        return $this->formattedValue ?? $value;
    }

    public function isOutsideComponent(): bool
    {
        return $this->outsideComponent;
    }

    public function isToOne(): bool
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

    /**
     * @throws Throwable
     */
    public function getResourceColumn(): string
    {
        return $this->getResource()?->getColumn() ?? 'id';
    }

    public function getRelatedModel(): ?Model
    {
        return $this->relatedModel;
    }

    /** @return Relation */
    public function getRelation(): ?Relation
    {
        return $this->getRelatedModel()
            ?->{$this->getRelationName()}();
    }

    protected function isOnChangeCondition(): bool
    {
        return ! $this->isOutsideComponent();
    }
}

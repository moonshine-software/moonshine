<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Fields\Relationships;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphOneOrMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use MoonShine\Contracts\Core\HasResourceContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\HasFieldsContract;
use MoonShine\Contracts\UI\RelationFieldContract;
use MoonShine\Core\Traits\HasResource;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\EnumToString;
use MoonShine\UI\Exceptions\FieldException;
use MoonShine\UI\Fields\Field;
use Throwable;

/**
 * @template-covariant R of BelongsTo|HasOneOrMany|HasOneOrManyThrough|BelongsToMany|MorphOneOrMany = BelongsTo|HasOneOrMany|HasOneOrManyThrough|BelongsToMany|MorphOneOrMany
 * @method static static make(Closure|string $label, ?string $relationName = null, Closure|string|null $formatted = null, class-string<ModelResource>|ModelResource|null $resource = null)
 *
 * @implements HasResourceContract<ModelResource>
 * @implements RelationFieldContract<Model>
 */
abstract class ModelRelationField extends Field implements RelationFieldContract, HasResourceContract
{
    /** @use HasResource<ModelResource> */
    use HasResource;

    protected string $relationName;

    protected ?Model $relatedModel = null;

    protected bool $outsideComponent = false;

    protected bool $toOne = false;

    protected bool $isMorph = false;

    /**
     * @var array<string, true>
     */
    public static array $excludeInstancing = [];

    /**
     * @throws Throwable
     * @param class-string<ModelResource>|ModelResource|null $resource
     */
    public function __construct(
        Closure|string $label,
        ?string $relationName = null,
        Closure|string|null $formatted = null,
        ModelResource|string|null $resource = null,
    ) {
        if (\is_string($formatted)) {
            $formatted = static fn ($item) => data_get($item, $formatted);
        }

        parent::__construct($label, $relationName, $formatted);

        $relationName ??= Str::of($this->getLabel())
            ->camel()
            ->when(
                $this->isToOne(),
                static fn (Stringable $str): Stringable => $str->singular(),
                static fn (Stringable $str): Stringable => $str->plural(),
            )->value();

        $this->setRelationName($relationName);

        if ($this->isToOne() && ! $this->isOutsideComponent()) {
            $this->setColumn(
                Str::of($this->getRelationName())
                    ->singular()
                    ->snake()
                    ->append('_id')
                    ->value(),
            );
        }

        if (\is_string($resource)) {
            $this->setResource(clone $this->findResource($resource));
        } elseif (\is_null($resource)) {
            $this->setResource(clone $this->findResource());
        } else {
            $this->setResource(clone $resource);
        }

        // required to create field entities and load assets
        if ($this instanceof HasFieldsContract && ! $this->isExcludeInstancing() && ! $this->isMorph()) {
            $this->excludeInstancing();
            $this->getResourceOrFail()->getFormFields();
        }
    }

    public function excludeInstancing(): void
    {
        self::$excludeInstancing[$this->getExcludeInstanceName()] = true;
    }

    private function getExcludeInstanceName(): string
    {
        return class_basename($this) . $this->getRelationName();
    }

    private function isExcludeInstancing(): bool
    {
        return isset(self::$excludeInstancing[$this->getExcludeInstanceName()]);
    }

    /**
     * @param  ?class-string<ModelResource>  $classString
     *
     * @throws Throwable
     */
    protected function findResource(?string $classString = null): ModelResource
    {
        if ($this->hasResource()) {
            return $this->getResource();
        }

        /** @var ?ModelResource $resource */
        $resource = $classString
            ? $this->getCore()->getResources()->findByClass($classString)
            : $this->getCore()->getResources()->findByUri(
                Str::of($this->getRelationName())
                    ->singular()
                    ->append('Resource')
                    ->kebab()
                    ->value(),
            );

        if (\is_null($resource) && $this->isMorph()) {
            /** @var ModelResource $resource */
            $resource = $this->getCore()->getResources()->findByUri(
                $this->getCore()->getCrudRequest()->getResourceUri() ?? '',
            );
        }

        return $resource ?? throw FieldException::resourceRequired(static::class, $this->getRelationName());
    }

    protected function prepareFill(array $raw = [], ?DataWrapperContract $casted = null): mixed
    {
        $model = $casted?->getOriginal();

        if (! $model instanceof Model) {
            return null;
        }

        $relationName = $this->getRelationName();

        if (! $model->isRelation($relationName)) {
            return null;
        }

        if (! $model->relationLoaded($relationName)) {
            $model->loadMissing($relationName);
        }

        return $model->getRelation($relationName);
    }

    /**
     * @throws Throwable
     */
    protected function resolveFill(array $raw = [], ?DataWrapperContract $casted = null, int $index = 0): static
    {
        if ($casted?->getOriginal() instanceof Model) {
            $this->setRelatedModel($casted->getOriginal());
        }

        $this->setData($casted);

        $data = $this->prepareFill($raw, $casted);

        $this->setValue($data);
        $this->setRowIndex($index);

        if ($this->isToOne()) {
            $relation = $this->getRelation();
            $this->setColumn(
                $relation instanceof BelongsTo || $relation instanceof HasOneOrMany
                    ? $relation->getForeignKeyName()
                    : '',
            );

            $this->setRawValue(
                $raw[$this->getColumn()] ?? null,
            );

            $this->setFormattedValue(
                data_get($data, $this->getResourceColumn()),
            );
        }

        if (! \is_null($this->afterFillCallback)) {
            return \call_user_func($this->afterFillCallback, $this);
        }

        return $this;
    }

    public function toFormattedValue(): mixed
    {
        $value = $this->toValue(withDefault: false);

        if ($this->isToOne() && ! \is_null($this->getFormattedValueCallback())) {
            $this->setFormattedValue(
                \call_user_func(
                    $this->getFormattedValueCallback(),
                    $value ?? $this->getRelation()?->getModel(),
                    $this->getRowIndex(),
                    $this,
                ),
            );
        }

        if ($this->isToOne()) {
            $value = data_get($value, $this->getResource()?->getColumn());
        }

        return new EnumToString(
            $this->formattedValue ?? $value
        )->convert();
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

    /**
     * @return null|Model
     */
    public function getRelated(): mixed
    {
        return $this->getRelatedModel();
    }

    public function getRelatedModel(): ?Model
    {
        return $this->relatedModel;
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $relations
     * @param  null|Model  $related
     *
     * @return null|Model
     */
    public function makeRelated(
        int|string|null $key = null,
        array $attributes = [],
        array $relations = [],
        mixed $related = null,
    ): mixed {
        return $this->makeRelatedModel($key, $attributes, $relations, $related);
    }

    /**
     * @param array<string, mixed> $attributes
     * @param array<string, mixed> $relations
     */
    public function makeRelatedModel(
        int|string|null $key = null,
        array $attributes = [],
        array $relations = [],
        ?Model $related = null,
    ): ?Model {
        $related ??= $this->getRelatedModel();

        if (\is_null($related)) {
            return null;
        }

        return $related->forceFill([
            $related->getKeyName() => $key,
            ...$attributes,
        ])->setRelations($relations);
    }

    /**
     * @return null|R
     */
    public function getRelation(): ?Relation
    {
        if ($this->getParent() instanceof self) {
            $related = $this->getParent()->getRelation()?->getRelated();

            if (! $related instanceof Model || ! $related->isRelation($this->getRelationName())) {
                return null;
            }

            return $this->getRelationFor($related);
        }

        $model = $this->getRelatedModel();

        if (! $model instanceof Model || ! $model->isRelation($this->getRelationName())) {
            return null;
        }

        return $this->getRelationFor($model);
    }

    /** @return R */
    public function getRelationFor(Model $model): Relation
    {
        /** @var R */
        return $model->{$this->getRelationName()}();
    }

    protected function isOnChangeCondition(): bool
    {
        return ! $this->isOutsideComponent();
    }

    protected function isSeeParams(): array
    {
        return [
            $this->getRelatedModel(),
        ];
    }
}

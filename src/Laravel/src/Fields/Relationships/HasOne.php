<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Fields\Relationships;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphOneOrMany;
use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\FieldWithComponentContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Contracts\UI\HasFieldsContract;
use MoonShine\Contracts\UI\TableBuilderContract;
use MoonShine\Core\Collections\Components;
use MoonShine\Crud\Contracts\Fields\HasModalModeContract;
use MoonShine\Crud\Contracts\Fields\HasOutsideSwitcherContract;
use MoonShine\Crud\Contracts\Fields\HasTabModeContract;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\Exceptions\ModelRelationFieldException;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Laravel\Traits\Fields\HasModalModeConcern;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\Support\Enums\PageType;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Contracts\HasUpdateOnPreviewContract;
use MoonShine\UI\Exceptions\FieldException;
use MoonShine\UI\Fields\Hidden;
use MoonShine\UI\Traits\Fields\HasTabModeConcern;
use MoonShine\UI\Traits\WithFields;
use Throwable;

/**
 * @template-covariant R of HasOneOrMany|HasOneOrManyThrough
 * @extends ModelRelationField<R>
 * @implements HasFieldsContract<Fields|FieldsContract>
 * @implements FieldWithComponentContract<FormBuilderContract>
 */
class HasOne extends ModelRelationField implements
    HasFieldsContract,
    FieldWithComponentContract,
    HasModalModeContract,
    HasTabModeContract,
    HasOutsideSwitcherContract
{
    use WithFields;
    use HasModalModeConcern;
    use HasTabModeConcern;

    protected string $view = 'moonshine::fields.relationships.has-one';

    protected bool $toOne = true;

    protected bool $isGroup = true;

    protected bool $hasOld = false;

    protected bool $resolveValueOnce = true;

    protected bool $outsideComponent = true;

    protected bool $isAsync = true;

    protected ?Closure $redirectAfter = null;

    protected ?Closure $modifyForm = null;

    protected ?Closure $modifyTable = null;

    protected ?FormBuilderContract $resolvedComponent = null;

    public function disableOutside(): static
    {
        $this->outsideComponent = false;

        return $this;
    }

    public function hasWrapper(): bool
    {
        return false;
    }

    public function async(): static
    {
        $this->isAsync = true;

        return $this;
    }

    public function disableAsync(): static
    {
        $this->isAsync = false;

        return $this;
    }

    public function isAsync(): bool
    {
        return $this->isAsync;
    }

    /**
     * Only for Preview
     * @throws Throwable
     */
    protected function prepareFields(): FieldsContract
    {
        $page = $this->getNowOnPage() ?? $this->getCore()->getCrudRequest()->findPage();

        if (! $this->hasFields()) {
            $fields = $page?->getPageType() === PageType::INDEX
                ? $this->getResource()->getIndexFields()
                : $this->getResource()->getDetailFields();

            $this->fields($fields->toArray());

            return $this->getFields();
        }

        /** @var Fields $fields */
        $fields = $this->getFields()->onlyFields(withWrappers: true);

        return $page?->getPageType() === PageType::INDEX
            ? $fields->indexFields()
            : $fields->detailFields();
    }

    protected function resolveRawValue(): mixed
    {
        $items = [$this->toValue()];

        return Collection::make($items)
            ->map(fn (Model $item) => data_get($item, $this->getResourceColumn()))
            ->implode(';');
    }

    /**
     * @throws Throwable
     */
    protected function resolvePreview(): Renderable|string
    {
        $items = [$this->toValue()];

        $resource = $this->getResource()->stopGettingItemFromUrl();

        $table = TableBuilder::make(items: $items)
            ->fields($this->getFieldsOnPreview())
            ->cast($resource->getCaster())
            ->preview()
            ->simple()
            ->vertical()
            ->when(
                ! \is_null($this->modifyTable),
                fn (TableBuilderContract $tableBuilder) => value($this->modifyTable, $tableBuilder)
            );

        if ($this->isModalMode()) {
            return $this->toValue() === null
                ? ''
                : (string)$this->getModalButton(
                    Components::make([$table]),
                    $this->getLabel(),
                    $this->getRelationName(),
                );
        }

        return $table->render();
    }

    /**
     * HasOne/HasMany mapper with updateOnPreview
     */
    private function getFieldsOnPreview(): Closure
    {
        return function () {
            $fields = $this->getPreparedFields();

            // the onlyFields method is needed to exclude stack fields
            $fields->onlyFields()->each(function (FieldContract $field): void {
                if ($field instanceof HasUpdateOnPreviewContract && $field->isUpdateOnPreview()) {
                    $field->nowOnParams(params: ['relation' => $this->getRelationName()]);
                }

                $field->setParent($this);
            });

            return $fields->toArray();
        };
    }

    /**
     * @param  Closure(int $parentId, static $field): string  $callback
     */
    public function redirectAfter(Closure $callback): static
    {
        $this->redirectAfter = $callback;

        return $this;
    }

    public function getRedirectAfter(Model|int|null|string $parentId): ?string
    {
        if (! \is_null($this->redirectAfter)) {
            return (string) \call_user_func($this->redirectAfter, $parentId, $this);
        }

        if ($this->isAsync() && ! \is_null($this->toValue())) {
            return null;
        }

        return $this->getDefaultRedirect($parentId);
    }

    public function getDefaultRedirect(Model|int|null|string $parentId): ?string
    {
        /** @var ?CrudResourceContract $resource */
        $resource = $this->getNowOnResource() ?? $this->getCore()->getCrudRequest()->getResource();

        return $resource->getFormPageUrl($parentId);
    }

    /**
     * @param  Closure(FormBuilderContract $table): FormBuilderContract  $callback
     */
    public function modifyForm(Closure $callback): static
    {
        $this->modifyForm = $callback;

        return $this;
    }

    /**
     * @param  Closure(TableBuilderContract $table): TableBuilderContract  $callback
     */
    public function modifyTable(Closure $callback): static
    {
        $this->modifyTable = $callback;

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function getFormModalButton(string $label, ?string $redirectBack = null): ActionButtonContract
    {
        return $this->getModalButton(
            Components::make([
                $this
                    ->redirectAfter(fn (): ?string => $redirectBack)
                    ->getComponent()
                    ->when(
                        $redirectBack === null,
                        fn (FormBuilderContract $form): FormBuilderContract => $form->withoutRedirect()
                    )
                    ->async(events: [
                        AlpineJs::event(
                            JsEvent::FRAGMENT_UPDATED,
                            $this->getRelationName()
                        ),
                    ]),
            ]),
            $label,
            $this->getRelationName()
        )->primary();
    }

    /**
     * @throws Throwable
     * @throws FieldException
     * @return FormBuilderContract
     */
    public function getComponent(): ComponentContract
    {
        if (! \is_null($this->resolvedComponent)) {
            return $this->resolvedComponent;
        }

        $resource = $this->getResource()->stopGettingItemFromUrl();

        /** @var ?ModelResource $parentResource */
        $parentResource = $this->getNowOnResource() ?? $this->getCore()->getCrudRequest()->getResource();

        $item = $this->toValue();

        // When need lazy load
        // $item->load($resource->getWith());

        if (\is_null($parentResource)) {
            throw ModelRelationFieldException::parentResourceRequired();
        }

        $parentItem = $parentResource->getItemOrInstance();
        /** @var HasOneOrMany|MorphOneOrMany $relation */
        $relation = $parentItem->{$this->getRelationName()}();

        $fields = $resource->getFormFields();
        $fields->onlyFields()->each(fn (FieldContract $field): FieldContract => $field->setParent($this));

        $action = $resource->getRoute(
            \is_null($item) ? 'crud.store' : 'crud.update',
            $item?->getKey()
        );

        $redirectAfter = $this->getRedirectAfter(
            $parentItem->getKey()
        );

        $isAsync = ! \is_null($item) && ($this->isAsync() || $resource->isAsync());

        return $this->resolvedComponent = FormBuilder::make($action)
            ->reactiveUrl(
                fn (): string => $this->getCore()->getRouter()
                    ->getEndpoints()
                    ->reactive(page: $resource->getFormPage(), resource: $resource, extra: ['key' => $item?->getKey()])
            )
            ->name($resource->getUriKey())
            ->switchFormMode($isAsync)
            ->fields(
                $fields->when(
                    ! \is_null($item),
                    static fn (FieldsContract $fields): FieldsContract => $fields->push(
                        Hidden::make('_method')->setValue('PUT'),
                    )
                )->push(
                    Hidden::make($relation->getForeignKeyName())
                        ->setValue($this->getRelatedModel()?->getKey())
                )->when(
                    $relation instanceof MorphOneOrMany,
                    fn (Fields $f) => $f->push(
                        /** @phpstan-ignore-next-line  */
                        Hidden::make($relation->getMorphType())->setValue($this->getRelatedModel()::class)
                    )
                )
                    ->toArray()
            )
            ->redirect($redirectAfter)
            ->fillCast(
                $item?->toArray() ?? array_filter([
                $relation->getForeignKeyName() => $this->getRelatedModel()?->getKey(),
                ...$relation instanceof MorphOneOrMany
                    ? [$relation->getMorphType() => $this->getRelatedModel()?->getMorphClass()]
                    : [],
            ], static fn ($value) => filled($value)),
                $resource->getCaster()
            )
            ->buttons(
                \is_null($item)
                    ? []
                    : [
                    $resource->getDeleteButton(
                        redirectAfterDelete: $this->getDefaultRedirect($parentItem->getKey()),
                        isAsync: false,
                        modalName: "has-one-{$this->getResource()->getUriKey()}-{$this->getRelationName()}",
                    ),
                ]
            )
            ->onBeforeFieldsRender(static fn (FieldsContract $fields): FieldsContract => $fields->exceptElements(
                static fn (ComponentContract $element): bool => $element instanceof ModelRelationField
                    && $element->isToOne()
                    && $element->getColumn() === $relation->getForeignKeyName()
            ))
            ->submit(__('moonshine::ui.save'), ['class' => 'btn-primary'])
            ->when(
                ! \is_null($this->modifyForm),
                fn (FormBuilderContract $form) => value($this->modifyForm, $form)
            );
    }

    /**
     * @throws Throwable
     */
    protected function resolveAfterDestroy(mixed $data): mixed
    {
        $this->getResource()
            ->getFormFields()
            ->onlyFields()
            ->each(fn (FieldContract $field): mixed => $field->setParent($this)->fillData($data)->afterDestroy($data));

        return $data;
    }

    public function isReactivitySupported(): bool
    {
        return false;
    }

    /**
     * @throws FieldException
     * @return array<string, mixed>
     * @throws Throwable
     */
    protected function viewData(): array
    {
        // On the form when outsideComponent is false,
        // the HasOne field can be displayed only in modalMode.
        if (! $this->outsideComponent) {
            $this->modalMode();
        }

        if (\is_null($this->getRelatedModel()?->getKey())) {
            return ['component' => ''];
        }

        return [
            'component' => $this->isModalMode()
                ? $this->getModalButton(
                    Components::make([$this->getComponent()]),
                    $this->getLabel(),
                    $this->getRelationName()
                )
                : $this->getComponent(),
        ];
    }
}

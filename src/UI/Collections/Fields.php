<?php

declare(strict_types=1);

namespace MoonShine\UI\Collections;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use MoonShine\Contracts\Collections\FieldsCollection;
use MoonShine\Contracts\Fields\FieldsWrapper;
use MoonShine\Contracts\Fields\Fileable;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\Fields\HasReactivity;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\ID;
use Throwable;

/**
 * @extends MoonShineRenderElements<int, Field>
 */
class Fields extends MoonShineRenderElements implements FieldsCollection
{
    /**
     * @throws Throwable
     */
    public function onlyFields(bool $withWrappers = false): static
    {
        $data = [];

        $this->extractFields($this->toArray(), $data);

        return static::make($data)->when(
            ! $withWrappers,
            fn (Fields $fields): static => $fields->withoutWrappers()
        );
    }

    /**
     * @throws Throwable
     */
    public function prepareAttributes(): static
    {
        return $this->onlyFields()
            ->map(
                static function (Field $formElement): Field {
                    $formElement->when(
                        ! $formElement instanceof Fileable,
                        function ($field): void {
                            $field->mergeAttribute('x-on:change', 'onChangeField($event)', ';');
                        }
                    );

                    return $formElement;
                }
            );
    }

    public function unwrapElements(string $class): static
    {
        $modified = self::make();

        $this->each(
            static function ($element) use ($class, $modified): void {
                if ($element instanceof $class) {
                    $element->getFields()->each(
                        fn ($inner): Collection => $modified->push($inner)
                    );
                } else {
                    $modified->push($element);
                }
            }
        );

        return $modified;
    }

    public function withoutWrappers(): static
    {
        return $this->unwrapElements(FieldsWrapper::class);
    }

    /**
     * @throws Throwable
     */
    public function whenFields(): static
    {
        return $this->filter(
            static fn (Field $field): bool => $field->hasShowWhen()
        )->values();
    }

    /**
     * @throws Throwable
     */
    public function whenFieldsConditions(): static
    {
        return $this->whenFields()->map(
            static fn (
                Field $field
            ): array => $field->showWhenCondition()
        );
    }

    /**
     * @throws Throwable
     */
    public function fillCloned(
        array $raw = [],
        mixed $casted = null,
        int $index = 0,
        ?Fields $preparedFields = null
    ): static {
        return ($preparedFields ?? $this->onlyFields())->map(
            fn (Field $field): Field => (clone $field)
                ->resolveFill($raw, $casted, $index)
        );
    }

    public function fillClonedRecursively(
        array $raw = [],
        mixed $casted = null,
        int $index = 0,
        ?Fields $preparedFields = null
    ): static {
        return ($preparedFields ?? $this)->map(function (MoonShineRenderable $component) use ($raw, $casted, $index) {
            if ($component instanceof HasFields) {
                $component = (clone $component)->fields(
                    $component->getFields()->fillClonedRecursively($raw, $casted, $index)
                );
            }

            if ($component instanceof Field) {
                $component->resolveFill($raw, $casted, $index);
            }

            return clone $component;
        });
    }

    /**
     * @throws Throwable
     */
    public function fill(array $raw = [], mixed $casted = null, int $index = 0): void
    {
        $this->onlyFields()->map(
            fn (Field $field): Field => $field
                ->resolveFill($raw, $casted, $index)
        );
    }

    /**
     * @throws Throwable
     */
    public function wrapNames(string $name): static
    {
        $this
            ->onlyFields()
            ->each(fn (Field $field): Field => $field->wrapName($name));

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function reset(): void
    {
        $this->onlyFields()->map(
            fn (Field $field): Field => $field->reset()
        );
    }

    public function onlyHasFields(): static
    {
        return $this->filter(static fn (Field $field): bool => $field instanceof HasFields);
    }

    public function withoutHasFields(): static
    {
        return $this->filter(static fn (Field $field): bool => ! $field instanceof HasFields);
    }

    /**
     * @throws Throwable
     */
    public function prepareReindex(?Field $parent = null, ?callable $before = null): static
    {
        return $this->map(function (Field $field) use ($parent, $before): Field {
            value($before, $parent, $field);

            $name = str($parent ? $parent->getNameAttribute() : $field->getNameAttribute());
            $level = $name->substrCount('$');

            if ($field instanceof ID) {
                $field->beforeRender(fn (ID $id): View|string => $id->preview());
            }

            $name = $name
                ->append('[${index' . $level . '}]')
                ->append($parent ? "[{$field->getColumn()}]" : '')
                ->replace('[]', '')
                ->when(
                    $field->getAttribute('multiple') || $field->isGroup(),
                    static fn (Stringable $str): Stringable => $str->append('[]')
                )->value();

            if ($parent) {
                $field
                    ->formName($parent?->getFormName())
                    ->setParent($parent);
            }

            return $field
                ->setNameAttribute($name)
                ->iterableAttributes($level);
        });
    }

    /**
     * @throws Throwable
     */
    public function reactiveFields(): static
    {
        return $this->filter(
            static fn (Field $field): bool => $field instanceof HasReactivity && $field->isReactive()
        );
    }

    /**
     * @return array<string, string>
     * @throws Throwable
     */
    public function extractLabels(): array
    {
        return $this->flatMap(
            static fn (Field $field): array => [$field->getColumn() => $field->getLabel()]
        )->toArray();
    }

    /**
     * @throws Throwable
     */
    public function findByColumn(
        string $column,
        Field $default = null
    ): ?Field {
        return $this->first(
            static fn (Field $field): bool => $field->getColumn() === $column,
            $default
        );
    }

    /**
     * @template-covariant T of Field
     * @param  class-string<T>  $class
     * @param ?Field  $default
     * @return Field
     * @throws Throwable
     */
    public function findByClass(
        string $class,
        Field $default = null
    ): ?Field {
        return $this->first(
            static fn (Field $field): bool => $field::class === $class,
            $default
        );
    }
}

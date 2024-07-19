<?php

declare(strict_types=1);

namespace MoonShine\UI\Collections;

use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\RenderableContract;
use MoonShine\Contracts\Core\TypeCasts\CastedDataContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\HasFieldsContract;
use MoonShine\Contracts\UI\HasReactivityContract;
use MoonShine\Core\Collections\Renderables;
use MoonShine\UI\Contracts\FieldsWrapperContract;
use MoonShine\UI\Contracts\FileableContract;
use MoonShine\UI\Fields\ID;
use Throwable;

/**
 * @extends Renderables<int, FieldContract>
 */
class Fields extends Renderables implements FieldsContract
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
            static fn (Fields $fields): static => $fields->withoutWrappers()
        );
    }

    /**
     * @throws Throwable
     */
    public function prepareAttributes(): static
    {
        return $this->onlyFields()
            ->map(
                static function (FieldContract $formElement): FieldContract {
                    $formElement->when(
                        ! $formElement instanceof FileableContract,
                        static function ($field): void {
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
                        static fn ($inner): Collection => $modified->push($inner)
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
        return $this->unwrapElements(FieldsWrapperContract::class);
    }

    /**
     * @throws Throwable
     */
    public function whenFields(): static
    {
        return $this->filter(
            static fn (FieldContract $field): bool => $field->hasShowWhen()
        )->values();
    }

    /**
     * @throws Throwable
     */
    public function whenFieldsConditions(): static
    {
        return $this->whenFields()->map(
            static fn (
                FieldContract $field
            ): array => $field->getShowWhenCondition()
        );
    }

    /**
     * @throws Throwable
     */
    public function fillCloned(
        array $raw = [],
        ?CastedDataContract $casted = null,
        int $index = 0,
        ?FieldsContract $preparedFields = null
    ): static {
        return ($preparedFields ?? $this->onlyFields())->map(
            static fn (FieldContract $field): FieldContract => (clone $field)
                ->fillData(is_null($casted) ? $raw : $casted, $index)
        );
    }

    public function fillClonedRecursively(
        array $raw = [],
        ?CastedDataContract $casted = null,
        int $index = 0,
        ?Fields $preparedFields = null
    ): static {
        return ($preparedFields ?? $this)->map(static function (RenderableContract $component) use ($raw, $casted, $index): RenderableContract {
            if ($component instanceof HasFieldsContract) {
                $component = (clone $component)->fields(
                    $component->getFields()->fillClonedRecursively($raw, $casted, $index)
                );
            }

            if ($component instanceof FieldContract) {
                $component->fillData(is_null($casted) ? $raw : $casted, $index);
            }

            return clone $component;
        });
    }

    /**
     * @throws Throwable
     */
    public function fill(array $raw = [], ?CastedDataContract $casted = null, int $index = 0): void
    {
        $this->onlyFields()->map(
            static fn (FieldContract $field): FieldContract => $field
                ->fillData(is_null($casted) ? $raw : $casted, $index)
        );
    }

    /**
     * @throws Throwable
     */
    public function wrapNames(string $name): static
    {
        $this
            ->onlyFields()
            ->each(static fn (FieldContract $field): FieldContract => $field->wrapName($name));

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function reset(): void
    {
        $this->onlyFields()->map(
            static fn (FieldContract $field): FieldContract => $field->reset()
        );
    }

    public function onlyHasFields(): static
    {
        return $this->filter(static fn (FieldContract $field): bool => $field instanceof HasFieldsContract);
    }

    public function withoutHasFields(): static
    {
        return $this->filter(static fn (FieldContract $field): bool => ! $field instanceof HasFieldsContract);
    }

    /**
     * @throws Throwable
     */
    public function prepareReindex(?FieldContract $parent = null, ?callable $before = null, ?callable $performName = null): static
    {
        return $this->map(static function (FieldContract $field) use ($parent, $before, $performName): FieldContract {
            $modifyField = value($before, $parent, $field);

            if($modifyField instanceof FieldContract) {
                $field = $modifyField;
            }

            $name = str($parent ? $parent->getNameDot() : $field->getNameDot());
            $level = $name->substrCount('$');

            if ($field instanceof ID) {
                $field->showValue();
            }

            $name = $field->generateNameFrom(
                $name->value(),
                "\${index$level}",
                $parent ? $field->getColumn() : null,
            );

            if($field->getAttribute('multiple') || $field->isGroup()) {
                $name .= '[]';
            }

            if ($parent) {
                $field
                    ->formName($parent->getFormName())
                    ->setParent($parent);
            }

            return $field
                ->setNameAttribute(
                    is_null($performName) ? $name : value($performName, $name, $parent, $field)
                )
                ->iterableAttributes($level);
        });
    }

    /**
     * @throws Throwable
     */
    public function reactiveFields(): static
    {
        return $this->filter(
            static fn (FieldContract $field): bool => $field instanceof HasReactivityContract && $field->isReactive()
        );
    }

    /**
     * @return array<string, string>
     * @throws Throwable
     */
    public function extractLabels(): array
    {
        return $this->flatMap(
            static fn (FieldContract $field): array => [$field->getColumn() => $field->getLabel()]
        )->toArray();
    }

    /**
     * @throws Throwable
     */
    public function findByColumn(
        string $column,
        FieldContract $default = null
    ): ?FieldContract {
        return $this->first(
            static fn (FieldContract $field): bool => $field->getColumn() === $column,
            $default
        );
    }

    /**
     * @template-covariant T of FieldContract
     * @param  class-string<T>  $class
     * @param ?FieldContract  $default
     * @throws Throwable
     */
    public function findByClass(
        string $class,
        FieldContract $default = null
    ): ?FieldContract {
        return $this->first(
            static fn (FieldContract $field): bool => $field::class === $class,
            $default
        );
    }
}

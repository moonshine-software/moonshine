<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Support\Collection;
use MoonShine\Collections\MoonShineRenderElements;
use MoonShine\Contracts\Fields\FieldsWrapper;
use MoonShine\Contracts\Fields\Fileable;
use MoonShine\Decorations\Decoration;
use Throwable;

/**
 * @extends MoonShineRenderElements<int, Field|Decoration>
 */
abstract class FormElements extends MoonShineRenderElements
{
    protected bool $onlyFieldsCalled = false;

    public function onlyFieldsCalled(): static
    {
        $this->onlyFieldsCalled = true;

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function onlyFields(bool $withWrappers = false): Fields
    {
        if ($this->onlyFieldsCalled) {
            return Fields::make($this->toArray())
                ->onlyFieldsCalled();
        }

        $data = [];

        $this->extractFields($this->toArray(), $data);

        return Fields::make($data)->when(
            ! $withWrappers,
            fn (Fields $fields): Fields|FormElements => $fields->withoutWrappers()
        )->onlyFieldsCalled();
    }

    /**
     * @throws Throwable
     */
    public function prepareAttributes(): Fields
    {
        return $this->onlyFields()
            ->map(
                static function (Field $formElement): Field {
                    $formElement->when(
                        ! $formElement instanceof Fileable,
                        function ($field): void {
                            $field->customAttributes(
                                ['x-on:change' => 'onChangeField($event)']
                            );
                        }
                    );

                    return $formElement;
                }
            );
    }

    public function unwrapElements(string $class): FormElements
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

    public function withoutWrappers(): FormElements|Fields
    {
        return $this->unwrapElements(FieldsWrapper::class);
    }

    /**
     * @throws Throwable
     */
    public function whenFields(): Fields
    {
        return $this->filter(
            static fn (Field $field): bool => $field->hasShowWhen()
        )->values();
    }

    /**
     * @throws Throwable
     */
    public function whenFieldsConditions(): Fields
    {
        return $this->whenFields()->map(
            static fn (
                Field $field
            ): array => $field->showWhenCondition()
        );
    }
}

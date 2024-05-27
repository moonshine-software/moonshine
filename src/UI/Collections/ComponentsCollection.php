<?php

declare(strict_types=1);

namespace MoonShine\UI\Collections;

use MoonShine\Contracts\Collections\FieldsCollection;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\MoonShineComponent;
use MoonShine\UI\Components\TableBuilder;
use Throwable;

/**
 * @template-covariant F of FieldsCollection
 * @extends MoonShineRenderElements<int, MoonShineComponent>
 */
final class ComponentsCollection extends MoonShineRenderElements
{
    /**
     * @throws Throwable
     */
    public function onlyForms(): self
    {
        $data = [];

        $this->extractOnly($this->toArray(), FormBuilder::class, $data);

        return self::make($data);
    }

    /**
     * @throws Throwable
     */
    public function onlyTables(): self
    {
        $data = [];

        $this->extractOnly($this->toArray(), TableBuilder::class, $data);

        return self::make($data);
    }

    /**
     * @throws Throwable
     */
    public function onlyComponents(): self
    {
        $data = [];

        $this->extractOnly($this->toArray(), MoonShineComponent::class, $data);

        return self::make($data);
    }

    /**
     * @throws Throwable
     * @return F
     */
    public function onlyFields(bool $withWrappers = false): FieldsCollection
    {
        return fieldsCollection($this->toArray())
            ->onlyFields($withWrappers);
    }

    /**
     * @throws Throwable
     */
    public function findForm(
        string $name,
        FormBuilder $default = null
    ): ?FormBuilder {
        return $this->onlyForms()->first(
            static fn (FormBuilder $component): bool => $component->getName() === $name,
            $default
        );
    }

    /**
     * @throws Throwable
     */
    public function findTable(
        string $name,
        TableBuilder $default = null
    ): ?TableBuilder {
        return $this->onlyTables()->first(
            static fn (TableBuilder $component): bool => $component->getName() === $name,
            $default
        );
    }

    /**
     * @throws Throwable
     */
    public function findByName(
        string $name,
        MoonShineComponent $default = null
    ): ?MoonShineComponent {
        return $this->onlyComponents()->first(
            static fn (MoonShineComponent $component): bool => $component->getName() === $name,
            $default
        );
    }
}

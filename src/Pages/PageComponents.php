<?php

declare(strict_types=1);

namespace MoonShine\Pages;

use MoonShine\Collections\MoonShineRenderElements;
use MoonShine\Components\FormBuilder;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Components\TableBuilder;
use MoonShine\Fields\Fields;
use MoonShine\Fields\FormElements;
use Throwable;

/**
 * @extends MoonShineRenderElements<int, MoonShineComponent>
 */
final class PageComponents extends MoonShineRenderElements
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
     */
    public function onlyFields(bool $withWrappers = false): Fields
    {
        $data = [];

        $this->extractFields($this->toArray(), $data);

        return Fields::make($data)->when(
            ! $withWrappers,
            fn (Fields $fields): Fields|FormElements => $fields->withoutWrappers()
        );
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

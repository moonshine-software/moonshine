<?php

declare(strict_types=1);

namespace MoonShine\Pages;

use MoonShine\Collections\MoonShineRenderElements;
use MoonShine\Components\FormBuilder;
use MoonShine\Components\MoonshineComponent;
use MoonShine\Components\TableBuilder;
use MoonShine\Fields\Fields;
use Throwable;

/**
 * @extends MoonShineRenderElements<int|string, MoonShineComponent>
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

        $this->extractOnly($this->toArray(), MoonshineComponent::class, $data);

        return self::make($data);
    }

    public function onlyFields(): Fields
    {
        $data = [];

        $this->extractFields($this->toArray(), $data);

        return Fields::make($data);
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
        MoonshineComponent $default = null
    ): ?MoonshineComponent {
        return $this->onlyComponents()->first(
            static fn (MoonshineComponent $component): bool => $component->getName() === $name,
            $default
        );
    }
}

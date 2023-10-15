<?php

declare(strict_types=1);

namespace MoonShine\Pages;

use MoonShine\Collections\MoonShineRenderElements;
use MoonShine\Components\FormBuilder;
use MoonShine\Components\MoonshineComponent;
use MoonShine\Components\TableBuilder;
use MoonShine\Decorations\Fragment;
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

    // TODO remove after decoration refactoring
    public function onlyFragments(): self
    {
        $data = [];

        $this->extractFragments($this->toArray(), $data);

        return self::make($data);
    }

    /**
     * @throws Throwable
     */
    //TODO replace return after decoration refactoring
    public function findByName(
        string $name,
        MoonshineComponent $default = null
    ): null|MoonshineComponent|Fragment {
        $component = $this->onlyComponents()->first(
            static fn (MoonshineComponent $component): bool => $component->getName() === $name,
            $default
        );

        if ($component) {
            return $component;
        }

        return $this->onlyFragments()->first(
            static fn (Fragment $fragment): bool => $fragment->getName() === $name,
            $default
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
}

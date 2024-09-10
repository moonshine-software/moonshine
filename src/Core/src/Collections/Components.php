<?php

declare(strict_types=1);

namespace MoonShine\Core\Collections;

use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\RenderableContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Contracts\UI\TableBuilderContract;
use MoonShine\Core\Traits\WithCore;
use Throwable;

/**
 * @template-covariant F of FieldsContract
 * @extends Renderables<array-key, RenderableContract>
 */
final class Components extends Renderables
{
    use WithCore;

    /**
     * @throws Throwable
     */
    public function onlyForms(): self
    {
        $data = [];

        $this->extractOnly($this->toArray(), FormBuilderContract::class, $data);

        return self::make($data);
    }

    /**
     * @throws Throwable
     */
    public function onlyTables(): self
    {
        $data = [];

        $this->extractOnly($this->toArray(), TableBuilderContract::class, $data);

        return self::make($data);
    }

    /**
     * @throws Throwable
     */
    public function onlyComponents(): self
    {
        $data = [];

        $this->extractOnly($this->toArray(), RenderableContract::class, $data);

        return self::make($data);
    }

    /**
     * @return F
     * @throws Throwable
     */
    public function onlyFields(bool $withWrappers = false): FieldsContract
    {
        return $this->getCore()
            ->getFieldsCollection($this->toArray())
            ->onlyFields($withWrappers);
    }

    /**
     * @throws Throwable
     */
    public function findForm(
        string $name,
        FormBuilderContract $default = null
    ): ?FormBuilderContract {
        return $this->onlyForms()->first(
            static fn (FormBuilderContract $component): bool => $component->getName() === $name,
            $default
        );
    }

    /**
     * @throws Throwable
     */
    public function findTable(
        string $name,
        TableBuilderContract $default = null
    ): ?TableBuilderContract {
        return $this->onlyTables()->first(
            static fn (TableBuilderContract $component): bool => $component->getName() === $name,
            $default
        );
    }

    /**
     * @throws Throwable
     */
    public function findByName(
        string $name,
        RenderableContract $default = null
    ): ?RenderableContract {
        return $this->onlyComponents()->first(
            static fn (RenderableContract $component): bool => $component->getName() === $name,
            $default
        );
    }
}

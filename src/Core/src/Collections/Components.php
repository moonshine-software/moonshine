<?php

declare(strict_types=1);

namespace MoonShine\Core\Collections;

use Illuminate\Support\Traits\Conditionable;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\HasComponentsContract;
use MoonShine\Contracts\UI\Collection\ComponentsContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Contracts\UI\HasFieldsContract;
use MoonShine\Contracts\UI\TableBuilderContract;
use MoonShine\Core\Traits\WithCore;
use Throwable;

/**
 * @template T of ComponentContract = ComponentContract
 * @template TFields of FieldsContract = FieldsContract<\MoonShine\Contracts\UI\FieldContract>
 *
 * @implements ComponentsContract<T, TFields>
 * @extends BaseCollection<T>
 */
final class Components extends BaseCollection implements ComponentsContract
{
    use WithCore;
    use Conditionable;

    /**
     * @template TType
     * @param  FieldsContract|ComponentsContract|iterable<array-key, ComponentContract>|self<T, TFields>  $elements
     * @param  class-string<TType>  $type
     * @param  list<TType>  $data
     *
     * @throws Throwable
     */
    protected function extractOnly(iterable $elements, string $type, array &$data): void
    {
        foreach ($elements as $element) {
            if ($element instanceof $type) {
                $data[] = $element;
            }

            if ($element instanceof HasFieldsContract) {
                $this->extractOnly($element->getFields(), $type, $data);
            } elseif ($element instanceof HasComponentsContract) {
                $this->extractOnly($element->getComponents(), $type, $data);
            }
        }
    }

    /**
     * @return static<FormBuilderContract>
     * @throws Throwable
     */
    public function onlyForms(): static
    {
        $data = [];

        $this->extractOnly($this, FormBuilderContract::class, $data);

        /**
         * @var static<FormBuilderContract>
         */
        return self::make($data);
    }

    /**
     * @return static<TableBuilderContract>
     * @throws Throwable
     */
    public function onlyTables(): static
    {
        $data = [];

        $this->extractOnly($this, TableBuilderContract::class, $data);

        /** @var static<TableBuilderContract> */
        return self::make($data);
    }

    /**
     * @return static<ComponentContract>
     * @throws Throwable
     */
    public function onlyComponents(): static
    {
        $data = [];

        $this->extractOnly($this, ComponentContract::class, $data);

        /** @var static<ComponentContract> */
        return self::make($data);
    }

    /**
     * @return FieldsContract<FieldContract>
     * @throws Throwable
     */
    public function onlyFields(bool $withWrappers = false, bool $withApplyWrappers = false): FieldsContract
    {
        return $this->getCore()
            ->getFieldsCollection($this)
            ->onlyFields($withWrappers, $withApplyWrappers);
    }

    /**
     * @throws Throwable
     */
    public function findForm(
        string $name,
        ?FormBuilderContract $default = null
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
        ?TableBuilderContract $default = null
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
        ?ComponentContract $default = null
    ): ?ComponentContract {
        return $this->onlyComponents()->first(
            static fn (ComponentContract $component): bool => $component->getName() === $name,
            $default
        );
    }
}

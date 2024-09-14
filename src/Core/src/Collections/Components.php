<?php

declare(strict_types=1);

namespace MoonShine\Core\Collections;

use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\HasComponentsContract;
use MoonShine\Contracts\UI\Collection\ComponentsContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Contracts\UI\HasFieldsContract;
use MoonShine\Contracts\UI\TableBuilderContract;
use MoonShine\Core\Traits\WithCore;
use Throwable;
use Closure;

/**
 * @template-covariant TFields of FieldsContract
 *
 */
final class Components extends Collection implements ComponentsContract
{
    use WithCore;
    use Conditionable;

    /**
     * @param  Closure(ComponentContract): bool  $except
     */
    public function exceptElements(Closure $except): static
    {
        return $this->filter(static function (ComponentContract $element) use ($except): bool {
            if ($except($element) === true) {
                return false;
            }

            if ($element instanceof HasFieldsContract) {
                $element->fields(
                    $element->getFields()->exceptElements($except)->toArray()
                );
            } elseif ($element instanceof HasComponentsContract) {
                $element->setComponents(
                    $element->getComponents()->exceptElements($except)->toArray()
                );
            }

            return true;
        })->filter()->values();
    }

    public function onlyVisible(): static
    {
        return $this->filter(
            static fn (ComponentContract $item): bool => $item->isSee()
        );
    }

    /**
     * @param  list<ComponentContract>  $elements
     * @param  list<ComponentContract>  $data
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

    public function toStructure(bool $withStates = true): array
    {
        return $this->map(
            static fn (ComponentContract $component): array => $component->toStructure($withStates)
        )->toArray();
    }

    /**
     * @throws Throwable
     */
    public function onlyForms(): static
    {
        $data = [];

        $this->extractOnly($this->toArray(), FormBuilderContract::class, $data);

        return self::make($data);
    }

    /**
     * @throws Throwable
     */
    public function onlyTables(): static
    {
        $data = [];

        $this->extractOnly($this->toArray(), TableBuilderContract::class, $data);

        return self::make($data);
    }

    /**
     * @throws Throwable
     */
    public function onlyComponents(): static
    {
        $data = [];

        $this->extractOnly($this->toArray(), ComponentContract::class, $data);

        return self::make($data);
    }

    /**
     * @return TFields
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
        ComponentContract $default = null
    ): ?ComponentContract {
        return $this->onlyComponents()->first(
            static fn (ComponentContract $component): bool => $component->getName() === $name,
            $default
        );
    }
}

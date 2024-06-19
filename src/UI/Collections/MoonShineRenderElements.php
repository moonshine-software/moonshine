<?php

declare(strict_types=1);

namespace MoonShine\UI\Collections;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\UI\Contracts\Components\HasComponents;
use MoonShine\UI\Contracts\Fields\HasFields;
use MoonShine\UI\Contracts\MoonShineRenderable;
use MoonShine\UI\Fields\Field;
use Throwable;

/**
 * @template-covariant T
 * @template TKey of array-key
 *
 * @extends Collection<TKey, T>
 */
abstract class MoonShineRenderElements extends Collection
{
    use Conditionable;

    /**
     * @throws Throwable
     */
    protected function extractOnly($elements, string $type, array &$data): void
    {
        foreach ($elements as $element) {
            if ($element instanceof $type) {
                $data[] = $element;
            }

            if ($element instanceof HasFields) {
                $this->extractOnly($element->getFields(), $type, $data);
            } elseif ($element instanceof HasComponents) {
                $this->extractOnly($element->getComponents(), $type, $data);
            }
        }
    }

    /**
     * @throws Throwable
     */
    protected function extractFields($elements, array &$data): void
    {
        foreach ($elements as $element) {
            if ($element instanceof Field) {
                $data[] = $element;
            } elseif ($element instanceof HasFields) {
                $this->extractFields($element->getFields(), $data);
            } elseif ($element instanceof HasComponents) {
                $this->extractFields($element->getComponents(), $data);
            }
        }
    }

    public function exceptElements(Closure $except): static
    {
        return $this->filter(static function ($element) use ($except): bool {
            if ($except($element) === true) {
                return false;
            }

            if ($element instanceof HasFields) {
                $element->fields(
                    $element->getFields()->exceptElements($except)->toArray()
                );
            } elseif ($element instanceof HasComponents) {
                $element->setComponents(
                    $element->getComponents()->exceptElements($except)->toArray()
                );
            }

            return true;
        })->filter()->values();
    }

    public function toStructure(bool $withStates = true): array
    {
        return $this->map(
            fn (MoonShineRenderable $component): array => $component->toStructure($withStates)
        )->toArray();
    }
}

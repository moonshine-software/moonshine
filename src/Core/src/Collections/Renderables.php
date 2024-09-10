<?php

declare(strict_types=1);

namespace MoonShine\Core\Collections;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Contracts\Core\HasComponentsContract;
use MoonShine\Contracts\Core\RenderableContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\HasFieldsContract;
use MoonShine\Contracts\UI\RenderablesContract;
use MoonShine\Contracts\UI\WithoutExtractionContract;
use Throwable;

/**
 * @template-covariant T
 *
 * @extends Collection<array-key, T>
 */
abstract class Renderables extends Collection implements RenderablesContract
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

            if ($element instanceof HasFieldsContract) {
                $this->extractOnly($element->getFields(), $type, $data);
            } elseif ($element instanceof HasComponentsContract) {
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
            if ($element instanceof FieldContract) {
                $data[] = $element;
            } elseif ($element instanceof HasFieldsContract && ! $element instanceof WithoutExtractionContract) {
                $this->extractFields($element->getFields(), $data);
            } elseif ($element instanceof HasComponentsContract && ! $element instanceof WithoutExtractionContract) {
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

    public function toStructure(bool $withStates = true): array
    {
        return $this->map(
            static fn (RenderableContract $component): array => $component->toStructure($withStates)
        )->toArray();
    }
}

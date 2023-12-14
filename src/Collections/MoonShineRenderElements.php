<?php

declare(strict_types=1);

namespace MoonShine\Collections;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Fields\Field;
use Throwable;

/**
 * @template T
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
            }
        }
    }

    public function exceptElements(Closure $except): self
    {
        return $this->filter(function ($element) use ($except): bool {
            if ($except($element) === true) {
                return false;
            }

            if ($element instanceof HasFields) {
                $element->fields(
                    $element->getFields()->exceptElements($except)->toArray()
                );
            }

            return true;
        })->filter()->values();
    }
}

<?php

declare(strict_types=1);

namespace MoonShine\Core\Collections;

use Closure;
use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\HasComponentsContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\HasFieldsContract;

/**
 * @template T of ComponentContract = ComponentContract
 * @extends Collection<array-key, T>
 */
abstract class BaseCollection extends Collection
{
    public function onlyVisible(): static
    {
        /** @var static */
        return $this->filter(static fn (ComponentContract $component): bool => $component->isSee());
    }

    /**
     * @param  Closure(ComponentContract): bool  $except
     */
    public function exceptElements(Closure $except): static
    {
        /** @var static */
        return $this->filter(static function (ComponentContract $element) use ($except): bool {
            if ($except($element) === true) {
                return false;
            }

            if ($element instanceof HasFieldsContract) {
                $element->fields(
                    $element->getFields()->exceptElements($except)
                );
            } elseif ($element instanceof HasComponentsContract) {
                $element->setComponents(
                    $element->getComponents()->exceptElements($except)
                );
            }

            return true;
        })->filter()->values();
    }

    /**
     * @return array<mixed>
     */
    public function toStructure(bool $withStates = true): array
    {
        return $this->map(
            static fn (ComponentContract $component): array => $component->toStructure($withStates)
        )->toArray();
    }
}

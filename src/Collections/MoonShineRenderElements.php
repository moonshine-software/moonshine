<?php

declare(strict_types=1);

namespace MoonShine\Collections;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Decorations\Decoration;
use MoonShine\Decorations\Tabs;
use Throwable;

abstract class MoonShineRenderElements extends Collection
{
    use Conditionable;

    /**
     * @throws Throwable
     */
    protected function extractOnly($elements, string $type, array &$data): void
    {
        foreach ($elements as $element) {
            if ($element instanceof Tabs) {
                foreach ($element->tabs() as $tab) {
                    $this->extractOnly($tab->getFields(), $type, $data);
                }
            } elseif ($element instanceof Decoration) {
                $this->extractOnly($element->getFields(), $type, $data);
            } elseif ($element instanceof $type) {
                $data[] = $element;
            }
        }
    }

    protected function exceptElements(Closure $except): self
    {
        return clone $this->map(function ($element) use ($except) {
            if ($except($element) === true) {
                return null;
            }

            if ($element instanceof Tabs) {
                foreach ($element->tabs() as $tab) {
                    $tab->fields(
                        $tab->getFields()->exceptElements($except)->toArray()
                    );
                }
            }

            if ($element instanceof HasFields) {
                $element->fields(
                    $element->getFields()->exceptElements($except)->toArray()
                );
            }

            return $element;
        })->filter()->values();
    }
}

<?php

declare(strict_types=1);

namespace MoonShine\Pages;

use Illuminate\Support\Collection;
use MoonShine\Collections\MoonShineRenderElements;
use MoonShine\Components\FormBuilder;
use Throwable;

/**
 * @template TKey of array-key
 * @template MoonShineComponent
 *
 * @extends  Collection<TKey, MoonShineComponent>
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
    public function findForm(
        string $name,
        FormBuilder $default = null
    ): ?FormBuilder {
        return $this->onlyForms()->first(
            static fn (FormBuilder $form): bool => $form->getName() === $name,
            $default
        );
    }
}

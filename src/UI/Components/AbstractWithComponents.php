<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use MoonShine\UI\Contracts\Components\HasComponents;
use MoonShine\UI\Traits\Components\WithComponents;
use Throwable;

/**
 * @method static static make(iterable $components = [])
 */
abstract class AbstractWithComponents extends MoonShineComponent implements HasComponents
{
    use WithComponents;

    /**
     * @throws Throwable
     */
    public function __construct(iterable $components = [])
    {
        parent::__construct();

        $this->setComponents($components);
    }

    /**
     * @return array<string, mixed>
     * @throws Throwable
     */
    protected function systemViewData(): array
    {
        return [
            ...parent::systemViewData(),
            'components' => $this->getComponents(),
        ];
    }
}

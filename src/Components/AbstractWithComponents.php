<?php

declare(strict_types=1);

namespace MoonShine\Components;

use MoonShine\Collections\MoonShineRenderElements;
use MoonShine\Contracts\Components\HasComponents;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Traits\WithComponents;
use MoonShine\Traits\WithFields;
use Throwable;

/**
 * @method static static make(iterable $components = [])
 */
abstract class AbstractWithComponents extends MoonShineComponent implements
    HasFields,
    HasComponents
{
    use WithFields;
    use WithComponents;

    /**
     * @throws Throwable
     */
    public function __construct(iterable $components = [])
    {
        parent::__construct();

        $this->setComponents($components);
        $this->fields($this->getComponents()->toArray());
    }

    public function getComponents(): MoonShineRenderElements
    {
        if($this->getFields()->isNotEmpty()) {
            return $this->getFields();
        }

        return $this->preparedComponents();
    }

    /**
     * @throws Throwable
     * @return array<string, mixed>
     */
    protected function systemViewData(): array
    {
        return [
            ...parent::systemViewData(),
            'components' => $this->getComponents(),
        ];
    }
}

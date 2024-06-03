<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use MoonShine\UI\Collections\MoonShineRenderElements;
use MoonShine\UI\Contracts\Components\HasComponents;
use MoonShine\UI\Contracts\Fields\HasAssets;
use MoonShine\UI\Contracts\Fields\HasFields;
use MoonShine\UI\Contracts\MoonShineRenderable;
use MoonShine\UI\Traits\Components\WithComponents;
use MoonShine\UI\Traits\WithFields;
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

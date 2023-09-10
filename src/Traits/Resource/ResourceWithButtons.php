<?php

declare(strict_types=1);

namespace MoonShine\Traits\Resource;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\ActionButtons\ActionButtons;

trait ResourceWithButtons
{
    /**
     * @return array<ActionButton>
     */
    public function getIndexButtons(): array
    {
        return empty($this->indexButtons()) ? $this->buttons() : $this->indexButtons();
    }

    /**
     * @return array<ActionButton>
     */
    public function getFormButtons(): array
    {
        return $this->withoutBulk($this->formButtons());
    }

    /**
     * @return array<ActionButton>
     */
    public function getDetailButtons(): array
    {
        return $this->withoutBulk($this->detailButtons());
    }

    /**
     * @return array<ActionButton>
     */
    protected function withoutBulk(array $buttonsType = []): array
    {
        return ActionButtons::make(
            empty($buttonsType)
                ? $this->buttons()
                : $buttonsType
        )
            ->withoutBulk()
            ->toArray()
        ;
    }

    /**
     * @return array<ActionButton>
     */
    public function buttons(): array
    {
        return [];
    }

    /**
     * @return array<ActionButton>
     */
    public function indexButtons(): array
    {
        return [];
    }

    /**
     * @return array<ActionButton>
     */
    public function formButtons(): array
    {
        return [];
    }

    /**
     * @return array<ActionButton>
     */
    public function detailButtons(): array
    {
        return [];
    }
}
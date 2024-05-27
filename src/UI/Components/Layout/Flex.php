<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use MoonShine\UI\Components\AbstractWithComponents;
use MoonShine\UI\Traits\Components\WithColumnSpan;

/**
 * @method static static make(iterable $components = [], int $colSpan = 12, int $adaptiveColSpan = 12, string $itemsAlign = 'center', string $justifyAlign = 'center', bool $withoutSpace = false)
 */
class Flex extends AbstractWithComponents
{
    use WithColumnSpan;

    protected string $view = 'moonshine::components.layout.flex';

    protected string $itemsAlign = 'center';

    protected string $justifyAlign = 'center';

    public function __construct(
        iterable $components = [],
        int $colSpan = 12,
        int $adaptiveColSpan = 12,
        string $itemsAlign = 'center',
        string $justifyAlign = 'center',
        protected bool $withoutSpace = false
    ) {
        $this
            ->justifyAlign($justifyAlign)
            ->itemsAlign($itemsAlign)
            ->columnSpan($colSpan, $adaptiveColSpan);

        parent::__construct($components);
    }

    public function withoutSpace(): self
    {
        $this->withoutSpace = true;

        return $this;
    }

    public function isWithoutSpace(): bool
    {
        return $this->withoutSpace;
    }

    public function itemsAlign(string $itemsAlign): self
    {
        $this->itemsAlign = $itemsAlign;

        return $this;
    }

    public function justifyAlign(string $justifyAlign): self
    {
        $this->justifyAlign = $justifyAlign;

        return $this;
    }

    public function getItemsAlign(): string
    {
        return $this->itemsAlign;
    }

    public function getJustifyAlign(): string
    {
        return $this->justifyAlign;
    }

    protected function viewData(): array
    {
        return [
            'isWithoutSpace' => $this->isWithoutSpace(),
            'itemsAlign' => $this->getItemsAlign(),
            'justifyAlign' => $this->getJustifyAlign(),
        ];
    }
}

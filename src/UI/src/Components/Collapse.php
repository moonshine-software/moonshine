<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Closure;
use Illuminate\View\ComponentSlot;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\HasIconContract;
use MoonShine\Contracts\UI\HasLabelContract;
use MoonShine\Contracts\UI\HasLabelEscapingContract;
use MoonShine\Support\DisplayHtml;
use MoonShine\UI\Enums\DisplayTextType;
use MoonShine\UI\Traits\WithIcon;
use MoonShine\UI\Traits\WithLabel;

/**
 * @method static static make(Closure|string $label = '', iterable<array-key, ComponentContract> $components = [], bool $open = false, bool $persist = true, ?bool $escapeLabel = null)
 */
class Collapse extends AbstractWithComponents implements HasIconContract, HasLabelContract, HasLabelEscapingContract
{
    use WithLabel;
    use WithIcon;

    protected string $view = 'moonshine::components.collapse';

    public function __construct(
        Closure|string $label = '',
        iterable $components = [],
        public bool $open = false,
        public bool $persist = true,
        ?bool $escapeLabel = null,
    ) {
        $this->setDisplayEscaping(DisplayTextType::LABEL, $escapeLabel);

        $this->setLabel($label);

        parent::__construct($components);
    }

    /** @param (Closure(static): (bool|null))|bool|null $condition */
    public function open(Closure|bool|null $condition = null): static
    {
        $this->open = value($condition, $this) ?? true;

        return $this;
    }

    public function isOpen(): bool
    {
        return $this->open;
    }

    /** @param (Closure(static): (bool|null))|bool|null $condition */
    public function persist(Closure|bool|null $condition = null): static
    {
        $this->persist = value($condition, $this) ?? true;

        return $this;
    }

    public function isPersist(): bool
    {
        return $this->persist;
    }

    /**
     * @param  array<string, mixed>  $customViewData
     * @return array<string, mixed>
     */
    protected function resolveCustomViewData(array $customViewData): array
    {
        if (\array_key_exists('title', $customViewData) && ! \array_key_exists('labelHtml', $customViewData)) {
            $customViewData['labelHtml'] = DisplayHtml::make($customViewData['title'], $this->isEscapeLabel());
        }

        return parent::resolveCustomViewData($customViewData);
    }

    protected function viewData(): array
    {
        return [
            'persist' => $this->isPersist(),
            'open' => $this->isOpen(),
            'title' => $this->getLabel(),
            'labelHtml' => $this->getLabelHtml(),
            'icon' => new ComponentSlot(
                $this->getIcon(5)
            ),
        ];
    }
}

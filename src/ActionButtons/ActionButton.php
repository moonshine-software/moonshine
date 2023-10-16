<?php

declare(strict_types=1);

namespace MoonShine\ActionButtons;

use Closure;
use MoonShine\Components\MoonshineComponent;
use MoonShine\Contracts\Actions\ActionButtonContract;
use MoonShine\Traits\InDropdownOrLine;
use MoonShine\Traits\WithIcon;
use MoonShine\Traits\WithLabel;
use MoonShine\Traits\WithModal;
use MoonShine\Traits\WithOffCanvas;

/**
 * @method static static make(Closure|string $label, Closure|string $url = '', mixed $item = null)
 */
class ActionButton extends MoonshineComponent implements ActionButtonContract
{
    use WithLabel;
    use WithIcon;
    use WithOffCanvas;
    use InDropdownOrLine;
    use WithModal;

    protected bool $isBulk = false;

    public function __construct(
        Closure|string $label,
        protected Closure|string $url = '',
        protected mixed $item = null
    ) {
        $this->setLabel($label);
    }

    public function getView(): string
    {
        return parent::getView() === ''
            ? 'moonshine::actions.default'
            : parent::getView();
    }

    protected function viewData(): array
    {
        return [
            'action' => $this,
        ];
    }

    public function blank(): self
    {
        $this->customAttributes([
            'target' => '_blank',
        ]);

        return $this;
    }

    public function bulk(): self
    {
        $this->isBulk = true;

        return $this;
    }

    public function isBulk(): bool
    {
        return $this->isBulk;
    }

    public function getItem(): mixed
    {
        return $this->item;
    }

    public function setItem(mixed $item): self
    {
        $this->item = $item;

        return $this;
    }

    public function onClick(Closure $onClick, ?string $modifier = null): self
    {
        $event = 'x-on:click';

        if (! is_null($modifier)) {
            $event .= ".$modifier";
        }

        $this->customAttributes([
            $event => $onClick($this->getItem()),
        ]);

        return $this;
    }

    public function url(): string
    {
        return value($this->url, $this->getItem());
    }

    /**
     * @return $this
     */
    public function primary(): static
    {
        return $this->customAttributes(['class' => 'btn-primary']);
    }

    /**
     * @return $this
     */
    public function secondary(): static
    {
        return $this->customAttributes(['class' => 'btn-secondary']);
    }

    /**
     * @return $this
     */
    public function success(): static
    {
        return $this->customAttributes(['class' => 'btn-success']);
    }

    /**
     * @return $this
     */
    public function warning(): static
    {
        return $this->customAttributes(['class' => 'btn-warning']);
    }

    /**
     * @return $this
     */
    public function error(): static
    {
        return $this->customAttributes(['class' => 'btn-error']);
    }
}

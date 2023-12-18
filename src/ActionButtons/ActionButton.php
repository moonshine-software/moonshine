<?php

declare(strict_types=1);

namespace MoonShine\ActionButtons;

use Closure;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Contracts\Actions\ActionButtonContract;
use MoonShine\Exceptions\ActionException;
use MoonShine\Support\Condition;
use MoonShine\Traits\InDropdownOrLine;
use MoonShine\Traits\WithIcon;
use MoonShine\Traits\WithLabel;
use MoonShine\Traits\WithModal;
use MoonShine\Traits\WithOffCanvas;
use Throwable;

/**
 * @method static static make(Closure|string $label, Closure|string $url = '', mixed $item = null)
 */
class ActionButton extends MoonShineComponent implements ActionButtonContract
{
    use WithLabel;
    use WithIcon;
    use WithOffCanvas;
    use InDropdownOrLine;
    use WithModal;

    protected bool $isBulk = false;

    protected bool $isAsync = false;

    public function __construct(
        Closure|string $label,
        protected Closure|string $url = '',
        protected mixed $item = null
    ) {
        $this->setLabel($label);
    }

    public static function emptyHidden(): self
    {
        return self::make('')
            ->customAttributes(['style' => 'display:none']);
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

    /**
     * @throws Throwable
     */
    public function method(
        string $method,
        ?string $message = null,
        ?string $selector = null,
        array $events = [],
        ?string $callback = null
    ): self {
        throw_if(!moonshineRequest()->hasResource(), ActionException::resourceRequired());

        $this->url = static fn(mixed $item): ?string => moonshineRequest()
            ->getResource()
            ?->route('async.method', $item?->getKey(), [
                'pageUri' => moonshineRequest()?->getPageUri(),
                'method' => $method,
                'message' => $message
            ]);

        return $this->async(
            selector: $selector,
            events: $events,
            callback: $callback
        );
    }

    public function isAsync(): bool
    {
        return $this->isAsync;
    }

    public function async(
        string $method = 'GET',
        ?string $selector = null,
        array $events = [],
        ?string $callback = null
    ): self {
        $this->isAsync = true;

        return $this->customAttributes([
            'x-data' => 'actionButton',
            'data-async-events' => collect($events)
                ->map(fn ($value): string => (string) str($value)->lower()->squish())
                ->filter()
                ->implode(','),
            'data-async-selector' => $selector,
            'data-async-callback' => $callback,
            'data-async-method' => $method,
        ])->onClick(fn (): string => 'request', 'prevent');
    }

    public function url(mixed $data = null): string
    {
        return value($this->url, $data ?? $this->getItem());
    }

    public function primary(Closure|bool|null $condition = null): self
    {
        if (! Condition::boolean($condition, true)) {
            return $this;
        }

        return $this->customAttributes(['class' => 'btn-primary']);
    }

    public function secondary(Closure|bool|null $condition = null): self
    {
        if (! Condition::boolean($condition, true)) {
            return $this;
        }

        return $this->customAttributes(['class' => 'btn-secondary']);
    }

    public function success(Closure|bool|null $condition = null): self
    {
        if (! Condition::boolean($condition, true)) {
            return $this;
        }

        return $this->customAttributes(['class' => 'btn-success']);
    }

    public function warning(Closure|bool|null $condition = null): self
    {
        if (! Condition::boolean($condition, true)) {
            return $this;
        }

        return $this->customAttributes(['class' => 'btn-warning']);
    }

    public function error(Closure|bool|null $condition = null): self
    {
        if (! Condition::boolean($condition, true)) {
            return $this;
        }

        return $this->customAttributes(['class' => 'btn-error']);
    }
}

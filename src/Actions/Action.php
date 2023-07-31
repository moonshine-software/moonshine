<?php

declare(strict_types=1);

namespace MoonShine\Actions;

use Closure;
use Illuminate\Contracts\View\View;
use MoonShine\Contracts\Actions\ActionContract;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Exceptions\ActionException;
use MoonShine\Traits\HasCanSee;
use MoonShine\Traits\InDropdownOrLine;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithIcon;
use MoonShine\Traits\WithLabel;
use MoonShine\Traits\WithView;

/**
 * @method static static make(string $label = '')
 */
abstract class Action implements ActionContract, MoonShineRenderable
{
    use Makeable;
    use WithView;
    use WithLabel;
    use WithIcon;
    use HasCanSee;
    use InDropdownOrLine;

    protected ?ResourceContract $resource = null;

    protected ?string $triggerKey = null;

    protected bool $withQuery = false;

    final public function __construct(string $label = '')
    {
        $this->setLabel($label);
    }

    abstract public function handle(): mixed;

    /**
     * @throws ActionException
     */
    public function url(): string
    {
        if (is_null($this->resource())) {
            throw new ActionException('Resource is required for action');
        }

        $query = [$this->getTriggerKey() => true];

        if ($this->withQuery()) {
            $query = array_merge(request()->query(), $query);
        }

        return '';
    }

    public function resource(): ?ResourceContract
    {
        return $this->resource;
    }

    public function getTriggerKey(): string
    {
        return $this->triggerKey ?? class_basename($this);
    }

    protected function withQuery(): bool
    {
        return $this->withQuery;
    }

    public function isTriggered(): bool
    {
        return request()->has($this->getTriggerKey());
    }

    public function setResource(ResourceContract $resource): static
    {
        $this->resource = $resource;

        return $this;
    }

    public function render(): View|Closure|string
    {
        return view(
            $this->getView() !== '' ? $this->getView()
                : 'moonshine::actions.default',
            [
                'action' => $this,
            ]
        );
    }

    public function __toString(): string
    {
        return (string) $this->render();
    }
}

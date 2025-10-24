<?php

declare(strict_types=1);

namespace MoonShine\Laravel\QueryTags;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Contracts\Core\HasCanSeeContract;
use MoonShine\Contracts\Core\HasCoreContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\HasIconContract;
use MoonShine\Contracts\UI\HasLabelContract;
use MoonShine\Core\Traits\WithCore;
use MoonShine\Laravel\Resources\CrudResource;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Traits\Makeable;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Traits\HasCanSee;
use MoonShine\UI\Traits\WithIcon;
use MoonShine\UI\Traits\WithLabel;

/**
 * @method static static make(Closure|string $label, Closure $builder)
 */
final class QueryTag implements HasCanSeeContract, HasIconContract, HasLabelContract, HasCoreContract
{
    use Makeable;
    use WithCore;
    use WithIcon;
    use HasCanSee;
    use WithLabel;

    protected bool $isDefault = false;

    protected ?string $alias = null;

    protected array $events = [];

    protected ?Closure $modifyButton = null;

    public function __construct(
        Closure|string $label,
        /** @var Closure(Builder): Builder $builder */
        protected Closure $builder,
    ) {
        $this->setLabel($label);
    }

    public function alias(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public function getUri(): string
    {
        if (! \is_null($this->alias)) {
            return $this->alias;
        }

        return str($this->getLabel())->slug()->value();
    }

    public function default(Closure|bool|null $condition = null): self
    {
        $this->isDefault = value($condition, $this) ?? true;

        return $this;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function isActive(): bool
    {
        if ($this->isDefault() && ! request()->filled('query-tag')) {
            return true;
        }

        return request()->getScalar('query-tag') === $this->getUri();
    }

    public function apply(Builder $builder): Builder
    {
        return \call_user_func($this->builder, $builder);
    }

    public function events(array $events): self
    {
        $this->events = $events;

        return $this;
    }

    /**
     * @param  Closure(ActionButtonContract $btn, self $ctx): ActionButtonContract  $callback
     *
     */
    public function modifyButton(Closure $callback): self
    {
        $this->modifyButton = $callback;

        return $this;
    }

    public function getButton(CrudResource $resource): ActionButtonContract
    {
        return ActionButton::make(
            $this->getLabel(),
            $resource->getIndexPageUrl(['query-tag' => $this->getUri()])
        )
            ->name("query-tag-{$this->getUri()}-button")
            ->showInLine()
            ->icon($this->getIconValue(), $this->isCustomIcon(), $this->getIconPath())
            ->canSee(fn (mixed $data): bool => $this->isSee())
            ->class('js-query-tag-button')
            ->xDataMethod('queryTag', 'btn-primary', $resource->getListEventName())
            ->when(
                $this->isActive(),
                static fn (ActionButtonContract $btn): ActionButtonContract => $btn
                    ->primary()
                    ->customAttributes([
                        'href' => $resource->getIndexPageUrl(),
                    ])
            )
            ->when(
                $resource->isAsync(),
                fn (ActionButtonContract $btn): ActionButtonContract => $btn
                    ->onClick(
                        fn ($action): string => "request(`{$this->getUri()}`)",
                        'prevent'
                    )
            )
            ->when(
                $this->isDefault(),
                static fn (ActionButtonContract $btn): ActionButtonContract => $btn->class('js-query-tag-default')
            )->when(
                $resource->isQueryTagsInDropdown(),
                fn (ActionButtonContract $btn): ActionButtonContract => $btn->showInDropdown()
            )->when(
                ! \is_null($this->modifyButton),
                fn (ActionButtonContract $btn): ActionButtonContract => \call_user_func($this->modifyButton, $btn, $this)
            )->when(
                $this->events !== [],
                fn (ActionButtonContract $btn): ActionButtonContract => $btn->customAttributes([
                    'data-async-events' => AlpineJs::prepareEvents(events: $this->events),
                ])
            );
    }
}

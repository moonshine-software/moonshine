<?php

declare(strict_types=1);

namespace MoonShine\Crud\QueryTags;

use Closure;
use Illuminate\Support\Str;
use MoonShine\Contracts\Core\HasCanSeeContract;
use MoonShine\Contracts\Core\HasCoreContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\HasIconContract;
use MoonShine\Contracts\UI\HasLabelContract;
use MoonShine\Core\Traits\WithCore;
use MoonShine\Crud\Contracts\Page\IndexPageContract;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Traits\Makeable;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Traits\HasCanSee;
use MoonShine\UI\Traits\WithIcon;
use MoonShine\UI\Traits\WithLabel;

/**
 * @template TBuilder of mixed = mixed
 * @method static static make(Closure|string $label, Closure $builder, string $prefix = '')
 */
class QueryTag implements HasCanSeeContract, HasIconContract, HasLabelContract, HasCoreContract
{
    use Makeable;
    use WithCore;
    use WithIcon;
    use HasCanSee;
    use WithLabel;

    protected bool $isDefault = false;

    protected ?string $alias = null;

    /**
     * @var string[]
     */
    protected array $events = [];

    protected ?Closure $modifyButton = null;

    public function __construct(
        Closure|string $label,
        /** @var Closure(TBuilder): TBuilder $builder */
        protected Closure $builder,
        protected string $prefix = '',
    ) {
        $this->setLabel($label);
    }

    public function prefix(string $prefix): static
    {
        $this->prefix = $prefix;

        return $this;
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

        return Str::of($this->getLabel())->slug()->value();
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
        if ($this->isDefault() && ! $this->getCore()->getRequest()->has($this->prefix . 'query-tag')) {
            return true;
        }

        return $this->getCore()->getRequest()->getScalar($this->prefix . 'query-tag') === $this->getUri();
    }

    /**
     * @param  TBuilder  $builder
     *
     * @return TBuilder
     */
    public function apply(mixed $builder): mixed
    {
        return \call_user_func($this->builder, $builder);
    }

    /**
     * @param  string[]  $events
     */
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

    public function getButton(IndexPageContract $page): ActionButtonContract
    {
        return ActionButton::make(
            $this->getLabel(),
            $page->getRoute([$this->prefix . 'query-tag' => $this->getUri()])
        )
            ->name("query-tag-{$this->getUri()}-button")
            ->showInLine()
            ->icon($this->getIconValue(), $this->isCustomIcon(), $this->getIconPath())
            ->canSee(fn (mixed $data): bool => $this->isSee())
            ->class('js-query-tag-button')
            ->xDataMethod('queryTag', 'btn-primary', $page->getListEventName())
            ->when(
                $this->isActive(),
                static fn (ActionButtonContract $btn): ActionButtonContract => $btn
                    ->primary()
                    ->customAttributes([
                        'href' => $page->getUrl(),
                    ])
            )
            ->when(
                $page->isAsync(),
                fn (ActionButtonContract $btn): ActionButtonContract => $btn
                    ->onClick(
                        fn ($action): string => "request(`{$this->getUri()}`, `$this->prefix`)",
                        'prevent'
                    )
            )
            ->when(
                $this->isDefault(),
                static fn (ActionButtonContract $btn): ActionButtonContract => $btn->class('js-query-tag-default')
            )->when(
                $page->isQueryTagsInDropdown(),
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

<?php

declare(strict_types=1);

namespace MoonShine\Crud\Components;

use JsonException;
use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\HasAsyncContract;
use MoonShine\Core\Traits\NowOn;
use MoonShine\Crud\Resources\CrudResource;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\DTOs\AsyncCallback;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\UI\Components\AbstractWithComponents;
use MoonShine\UI\Traits\HasAsync;
use Throwable;

class Fragment extends AbstractWithComponents implements HasAsyncContract
{
    use HasAsync;
    use NowOn;

    protected string $view = 'moonshine::components.fragment';

    /**
     * @param  iterable<array-key, ComponentContract>  $components
     *
     * @throws Throwable
     */
    public function __construct(iterable $components = [])
    {
        parent::__construct($components);

        $this->async(function (Fragment $fragment): mixed {
            $page = $fragment->getNowOnPage() ?? $this->getCore()->getCrudRequest()->findPage();
            $resource = $fragment->getNowOnResource() ?? $this->getCore()->getCrudRequest()->getResource();

            if (\is_null($page) && \is_null($resource)) {
                return $this->getCore()->getRequest()->getUrlWithQuery([
                    '_fragment-load' => $fragment->getName(),
                ]);
            }

            $params = $fragment->getNowOnQueryParams();
            $itemID = $params['resourceItem'] ?? $this->getCore()->getCrudRequest()->getItemID();

            return $this->getCore()->getRouter()->getEndpoints()->toPage(
                page: $fragment->getNowOnPage() ?? $this->getCore()->getCrudRequest()->getPage(),
                resource: $fragment->getNowOnResource() ?? $this->getCore()->getCrudRequest()->getResource(),
                params: array_filter([
                    ...$fragment->getNowOnQueryParams(),
                    'resourceItem' => $itemID,
                ]),
                extra: [
                    'fragment' => $fragment->getName(),
                ]
            );
        });
    }

    /**
     * @param array<string, mixed> $params
     * @param CrudResourceContract|null $resource
     * @param PageContract|null $page
     * @param string|string[]|null $events
     * @throws Throwable
     */
    public function updateWith(
        array $params = [],
        CrudResourceContract|null $resource = null,
        PageContract|null $page = null,
        string|array|null $events = null,
        ?AsyncCallback $callback = null,
    ): static {
        /** @var ?CrudResource $resource */
        $resource ??= $this->getCore()->getCrudRequest()->getResource();
        $page ??= $this->getCore()->getCrudRequest()->getPage();

        $this->asyncEvents = $events;
        $this->asyncCallback = $callback;

        return $this->nowOn(
            $page,
            $resource,
            $params
        );
    }

    public function withQueryParams(): static
    {
        return $this->customAttributes(
            AlpineJs::asyncWithQueryParamsAttributes()
        );
    }

    /**
     * @param  array<string, string> $selectors
     */
    public function withSelectorsParams(array $selectors): static
    {
        return $this->customAttributes(
            AlpineJs::asyncSelectorsParamsAttributes($selectors)
        );
    }

    /**
     * @throws JsonException
     */
    protected function prepareBeforeRender(): void
    {
        parent::prepareBeforeRender();

        $this->xDataMethod('fragment', $this->getAsyncUrl());
        $this->customAttributes([
            AlpineJs::eventBlade(JsEvent::FRAGMENT_UPDATED, $this->getName())
            => 'fragmentUpdate(`' . $this->getAsyncEvents() . '`,' . json_encode(
                $this->getAsyncCallback(),
                JSON_THROW_ON_ERROR
            ) . ')',
        ]);
    }
}

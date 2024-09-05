<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Components;

use Illuminate\Http\RedirectResponse;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Core\Traits\NowOn;
use MoonShine\Laravel\Resources\CrudResource;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\UI\Components\AbstractWithComponents;
use MoonShine\UI\Traits\HasAsync;
use Throwable;

/**
 * @method static static make(array $fields = [])
 */
class Fragment extends AbstractWithComponents
{
    use HasAsync;
    use NowOn;

    protected string $view = 'moonshine::components.fragment';

    public function __construct(iterable $components = [])
    {
        parent::__construct($components);

        $this->async(static fn (Fragment $fragment): RedirectResponse|string => toPage(
            page: $fragment->getNowOnPage() ?? moonshineRequest()->getPage(),
            resource: $fragment->getNowOnResource() ?? moonshineRequest()->getResource(),
            params: $fragment->getNowOnQueryParams(),
            fragment: $fragment->getName()
        ));
    }

    /**
     * @throws Throwable
     */
    public function updateWith(
        array $params = [],
        string|ResourceContract|null $resource = null,
        string|PageContract|null $page = null,
    ): static {
        /** @var CrudResource $resource */
        $resource ??= moonshineRequest()->getResource();
        $page ??= moonshineRequest()->getPage();

        return $this->nowOn(
            $page,
            $resource,
            $params
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

    protected function prepareBeforeRender(): void
    {
        parent::prepareBeforeRender();

        $this->xDataMethod('fragment', $this->getAsyncUrl());
        $this->customAttributes([
            AlpineJs::eventBlade(JsEvent::FRAGMENT_UPDATED, $this->getName()) => 'fragmentUpdate',
        ]);
    }
}

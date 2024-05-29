<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Components;

use Illuminate\Http\RedirectResponse;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Core\Pages\Page;
use MoonShine\Core\Traits\NowOn;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\Support\Traits\HasAsync;
use MoonShine\UI\Components\AbstractWithComponents;
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

        $this->async(fn (Fragment $fragment): RedirectResponse|string => toPage(
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
        string|Page|null $page = null,
    ): static {
        /** @var ModelResource $resource */
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
    public function withSelectorsParams(array $selectors): self
    {
        return $this->customAttributes(
            AlpineJs::asyncSelectorsParamsAttributes($selectors)
        );
    }

    protected function prepareBeforeRender(): void
    {
        parent::prepareBeforeRender();

        $this->customAttributes([
            'x-data' => 'fragment(`' . $this->getAsyncUrl() . '`)',
            AlpineJs::eventBlade(JsEvent::FRAGMENT_UPDATED, $this->getName()) => 'fragmentUpdate',
        ]);
    }
}

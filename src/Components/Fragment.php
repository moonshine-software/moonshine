<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Illuminate\Http\RedirectResponse;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Enums\JsEvent;
use MoonShine\Pages\Page;
use MoonShine\Resources\ModelResource;
use MoonShine\Support\AlpineJs;
use MoonShine\Traits\HasAsync;
use MoonShine\Traits\NowOn;
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

        $this->async(fn(Fragment $fragment): RedirectResponse|string => toPage(
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

    protected function prepareBeforeRender(): void
    {
        parent::prepareBeforeRender();

        $this->customAttributes([
            'x-data' => 'fragment(`' . $this->getAsyncUrl() . '`)',
            AlpineJs::eventBlade(JsEvent::FRAGMENT_UPDATED, $this->getName()) => 'fragmentUpdate',
        ]);
    }
}

<?php

declare(strict_types=1);

namespace MoonShine\Components;

use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Enums\JsEvent;
use MoonShine\Exceptions\MoonShineComponentException;
use MoonShine\Exceptions\PageException;
use MoonShine\Pages\Page;
use MoonShine\Resources\ModelResource;
use MoonShine\Support\AlpineJs;
use Throwable;

/**
 * @method static static make(array $fields = [])
 */
class Fragment extends AbstractWithComponents
{
    protected string $url = '';

    protected string $view = 'moonshine::components.fragment';

    /**
     * @throws MoonShineComponentException
     * @throws PageException
     * @throws Throwable
     */
    public function name(string $name): static
    {
        return parent::name($name)->updateWithParams();
    }

    /**
     * @throws MoonShineComponentException
     * @throws PageException
     * @throws Throwable
     */
    public function updateWithParams(
        array $params = [],
        string|ResourceContract|null $resource = null,
        string|Page|null $page = null,
    ): static {
        if(is_null($this->getName())) {
            throw new MoonShineComponentException("To use updateAsync you must first give the fragment a name");
        }

        /** @var ModelResource $resource */
        $resource ??= moonshineRequest()->getResource();

        $page ??= moonshineRequest()->getPage();

        if(is_null($resource) && is_null($page)) {
            throw new PageException("Resource or FormPage not found when generating updateAsyncUrl");
        }

        $this->url = toPage(
            page: $page,
            resource: $resource,
            params: $params,
            fragment: $this->getName()
        );

        return $this;
    }

    protected function getUrl(): string
    {
        return $this->url;
    }

    protected function prepareBeforeRender(): void
    {
        parent::prepareBeforeRender();

        $this->customAttributes([
            'x-data' => 'fragment(`' . $this->getUrl() . '`)',
            AlpineJs::eventBlade(JsEvent::FRAGMENT_UPDATED, $this->getName()) => 'fragmentUpdate',
        ]);
    }
}

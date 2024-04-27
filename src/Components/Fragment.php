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

/**
 * @method static static make(array $fields = [])
 */
class Fragment extends AbstractWithComponents
{
    protected bool $isUpdateAsync = false;

    protected string $updateAsyncUrl = '';

    protected string $view = 'moonshine::components.fragment';

    /**
     * @throws MoonShineComponentException
     * @throws PageException
     */
    public function name(string $name): static
    {
        return parent::name($name)->updateAsync();
    }

    /**
     * @throws MoonShineComponentException
     * @throws PageException
     */
    public function updateAsync(
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

        $this->updateAsyncUrl = to_page(
            page: $page,
            resource: $resource,
            params: $params,
            fragment: $this->getName()
        );

        $this->isUpdateAsync = true;

        return $this;
    }

    protected function isUpdateAsync(): bool
    {
        return $this->isUpdateAsync;
    }

    protected function updateAsyncUrl(): string
    {
        return $this->updateAsyncUrl;
    }

    protected function prepareBeforeRender(): void
    {
        parent::prepareBeforeRender();

        if($this->isUpdateAsync()) {
            $this->customAttributes([
                'x-data' => 'fragment(`' . $this->updateAsyncUrl() . '`)',
                AlpineJs::eventBlade(JsEvent::FRAGMENT_UPDATED, $this->getName()) => 'fragmentUpdate',
            ]);
        }
    }
}

<?php

declare(strict_types=1);

namespace MoonShine;

use Illuminate\Http\Request;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Pages\Page;

class MoonShineRequest extends Request
{
    public function hasResource(): bool
    {
        return ! is_null($this->getResource());
    }

    public function getResource(): ?ResourceContract
    {
        return memoize(fn(): ?ResourceContract => moonshine()->getResourceFromUriKey(
            $this->getResourceUri()
        )?->boot());
    }

    public function getItemID(): int|string|null
    {
        return request(
            'resourceItem',
            request()->route('resourceItem')
        );
    }

    public function findPage(): ?Page
    {
        return memoize(function (): ?Page {
            if ($this->hasResource()) {
                return $this->getResource()
                    ?->getPages()
                    ?->findByUri($this->getPageUri());
            }

            return moonshine()->getPageFromUriKey(
                $this->getPageUri()
            );
        });
    }

    public function getPage(): Page
    {
        $page = $this->findPage();

        if (is_null($page)) {
            oops404();
        }

        return $page;
    }

    public function getResourceUri(): ?string
    {
        return $this->route('resourceUri');
    }

    public function getParentResourceId(): ?string
    {
        return request('_parentId');
    }

    public function getParentRelationName(): ?string
    {
        return
            is_null($parentResource = $this->getParentResourceId())
                ? null
                : explode('-', $parentResource)[0] ?? null;
    }

    public function getParentRelationId(): int|string|null
    {
        return
            is_null($parentResource = $this->getParentResourceId())
                ? null
                : explode('-', $parentResource)[1] ?? null;
    }

    public function getPageUri(): ?string
    {
        return $this->route('pageUri');
    }

    public function onResourceRoute(): bool
    {
        return str($this->url())->contains('resource/');
    }

    public function getFragmentLoad(): ?string
    {
        return request('_fragment-load');
    }

    public function isFragmentLoad(?string $name = null): bool
    {
        $fragment = $this->getFragmentLoad();

        if (! is_null($fragment) && ! is_null($name)) {
            return $fragment === $name;
        }

        return ! is_null($fragment);
    }

    public function isMoonShineRequest(): bool
    {
        return in_array(
            'moonshine',
            $this->route()?->gatherMiddleware() ?? [],
            true
        );
    }
}

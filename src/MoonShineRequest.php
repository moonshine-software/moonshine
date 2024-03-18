<?php

declare(strict_types=1);

namespace MoonShine;

use Illuminate\Http\Request;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Pages\Page;
use MoonShine\Traits\Request\HasPageRequest;
use MoonShine\Traits\Request\HasResourceRequest;

class MoonShineRequest extends Request
{
    use HasResourceRequest, HasPageRequest;

    public function getItemID(): int|string|null
    {
        return request(
            'resourceItem',
            request()->route('resourceItem')
        );
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

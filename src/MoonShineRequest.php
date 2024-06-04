<?php

declare(strict_types=1);

namespace MoonShine;

use Illuminate\Http\Request;
use MoonShine\Http\Controllers\RelationModelFieldController;
use MoonShine\Traits\Request\HasPageRequest;
use MoonShine\Traits\Request\HasResourceRequest;

class MoonShineRequest extends Request
{
    use HasResourceRequest;
    use HasPageRequest;

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
        if (is_null($parentResource = $this->getParentResourceId())) {
            return null;
        }

        return str($parentResource)
            ->replace('-' . $this->getParentRelationId(), '')
            ->camel()
            ->value();
    }

    public function getComponentName(): string
    {
        return request()
            ->str('_component_name')
            /**
             * @see RelationModelFieldController::hasManyForm() Unique formName
             */
            ->before('-unique-')
            ->value();
    }

    public function getParentRelationId(): int|string|null
    {
        return
            is_null($parentResource = $this->getParentResourceId())
                ? null
                : str($parentResource)->explode("-")->last();
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

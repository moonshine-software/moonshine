<?php

declare(strict_types=1);

namespace MoonShine\Laravel;

use Illuminate\Http\Request;
use MoonShine\Laravel\Traits\Request\HasPageRequest;
use MoonShine\Laravel\Traits\Request\HasResourceRequest;

class MoonShineRequest extends Request
{
    use HasResourceRequest;
    use HasPageRequest;

    public function getItemID(): int|string|null
    {
        return request('resourceItem');
    }

    public function getParentResourceId(): ?string
    {
        return request('_parentId');
    }

    public function getParentRelationName(): ?string
    {
        if(is_null($parentResource = $this->getParentResourceId())) {
            return null;
        }

        return str($parentResource)
            ->replace('-' . $this->getParentRelationId(), '')
            ->camel()
            ->value();
    }

    public function getParentRelationId(): int|string|null
    {
        return
            is_null($parentResource = $this->getParentResourceId())
                ? null
                : str($parentResource)->explode("-")->last();
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

    public function isOnResourceRoute(): bool
    {
        return str($this->url())->contains('resource/');
    }

    public function getFragmentLoad(): ?string
    {
        return request()->input('_fragment-load');
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

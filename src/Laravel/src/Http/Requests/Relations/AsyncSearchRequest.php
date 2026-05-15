<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Requests\Relations;

use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Support\Enums\Ability;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\Enums\PageType;
use Throwable;

class AsyncSearchRequest extends RelationModelFieldRequest
{
    /**
     * @throws Throwable
     */
    public function authorize(): bool
    {
        $resource = $this->getResource();

        if (\is_null($resource)) {
            throw ResourceException::notDeclared();
        }

        $ability = match ($this->getPage()->getPageType()) {
            PageType::INDEX => Ability::VIEW_ANY,
            PageType::DETAIL => Ability::VIEW,
            PageType::FORM => $this->route('resourceItem') === null
                ? Ability::CREATE
                : Ability::UPDATE,
            default => Ability::VIEW_ANY,
        };

        $action = match ($ability) {
            Ability::VIEW => Action::VIEW,
            Ability::CREATE => Action::CREATE,
            Ability::UPDATE => Action::UPDATE,
            default => null,
        };

        if ($action !== null && ! $resource->hasAction($action)) {
            return false;
        }

        return $resource->can($ability);
    }
}

<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Requests\Resources;

use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Laravel\Http\Requests\MoonShineFormRequest;
use MoonShine\Support\Enums\Ability;
use MoonShine\Support\Enums\Action;
use Throwable;

final class MassDeleteFormRequest extends MoonShineFormRequest
{
    /**
     * @throws ResourceException
     * @throws Throwable
     */
    public function authorize(): bool
    {
        $this->beforeResourceAuthorization();

        $resource = $this->getResource();

        if (\is_null($resource)) {
            return false;
        }

        if (! $resource->hasAction(Action::MASS_DELETE)) {
            return false;
        }

        return $resource->can(Ability::MASS_DELETE);
    }

    /**
     * @return array{ids: string[]}
     */
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array'],
        ];
    }
}

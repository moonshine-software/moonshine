<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Requests\Resources;

use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Laravel\Enums\Ability;
use MoonShine\Laravel\Enums\Action;
use MoonShine\Laravel\Http\Requests\MoonShineFormRequest;
use Throwable;

final class UpdateFormRequest extends MoonShineFormRequest
{
    /**
     * @throws Throwable
     * @throws ResourceException
     */
    public function authorize(): bool
    {
        $this->beforeResourceAuthorization();

        if (! $this->getResource()?->hasAction(Action::UPDATE)) {
            return false;
        }

        return $this->getResource()?->can(Ability::UPDATE) ?? false;
    }

    public function rules(): array
    {
        return $this->getResource()?->getRules() ?? [];
    }
}

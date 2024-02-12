<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Resources;

use MoonShine\Exceptions\ResourceException;
use MoonShine\Http\Requests\MoonShineFormRequest;
use Throwable;

final class ViewAnyFormRequest extends MoonShineFormRequest
{
    /**
     * @throws Throwable
     * @throws ResourceException
     */
    public function authorize(): bool
    {
        $this->beforeResourceAuthorization();

        return $this->getResource()?->can('viewAny') ?? false;
    }
}

<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Requests\Resources;

use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Laravel\Http\Requests\MoonShineFormRequest;
use Throwable;

final class ViewFormRequest extends MoonShineFormRequest
{
    /**
     * @throws Throwable
     * @throws ResourceException
     */
    public function authorize(): bool
    {
        $this->beforeResourceAuthorization();

        if (! in_array(
            'view',
            $this->getResource()?->getActiveActions() ?? [],
            true
        )) {
            return false;
        }

        return $this->getResource()?->can('view') ?? false;
    }
}

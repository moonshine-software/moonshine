<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Requests\Resources;

use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Laravel\Http\Requests\MoonShineFormRequest;
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

        return $this->getResource()?->can('massDelete') ?? false;
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

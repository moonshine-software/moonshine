<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Resources;

use MoonShine\Http\Requests\MoonShineFormRequest;

final class MassDeleteFormRequest extends MoonShineFormRequest
{
    public function authorize(): bool
    {
        $this->beforeResourceAuthorization();

        return $this->getResource()->can('massDelete');
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

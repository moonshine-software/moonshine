<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Resources;

use MoonShine\MoonShineRequest;

final class MassDeleteFormRequest extends MoonShineRequest
{
    public function authorize(): bool
    {
        return $this->getResource()->can('massDelete');
    }

    public function rules(): array
    {
        return [
            'ids' => ['required', 'array'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'ids' => request()
                ->str('ids')
                ->explode(';')
                ->filter()
                ->toArray(),
        ]);
    }
}

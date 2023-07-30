<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Resources;

use MoonShine\Http\Requests\MoonshineFormRequest;

final class MassDeleteFormFormRequest extends MoonshineFormRequest
{
    public function authorize(): bool
    {
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

    protected function prepareForValidation(): void
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

<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Resources;

use MoonShine\MoonShineRequest;

final class CreateFormRequest extends MoonShineRequest
{
    public function authorize(): bool
    {
        if (! in_array(
            'create',
            $this->getResource()->getActiveActions(),
            true
        )) {
            return false;
        }

        return $this->getResource()->can('create');
    }

    public function rules(): array
    {
        return [];
    }
}

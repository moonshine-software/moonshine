<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Resources;

use MoonShine\Http\Requests\MoonShineFormRequest;

final class StoreFormRequest extends MoonShineFormRequest
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
        return $this->getResource()->rules($this->getResource()->getModel());
    }
}

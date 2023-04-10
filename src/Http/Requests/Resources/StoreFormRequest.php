<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Resources;

use MoonShine\MoonShineRequest;

final class StoreFormRequest extends MoonShineRequest
{
    public function authorize(): bool
    {
        if (! in_array('create', $this->getResource()->getActiveActions(), true)) {
            return false;
        }

        return $this->getResource()->can('create', $this->getResource()->getModel());
    }

    public function rules(): array
    {
        return $this->getResource()->rules($this->getResource()->getModel());
    }
}

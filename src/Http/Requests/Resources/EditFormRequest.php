<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Resources;

use MoonShine\MoonShineRequest;

final class EditFormRequest extends MoonShineRequest
{
    public function authorize(): bool
    {
        if (! in_array('edit', $this->getResource()->getActiveActions(), true)) {
            return false;
        }

        return $this->getResource()->can('update', $this->getItemOrFail());
    }

    public function rules(): array
    {
        return [];
    }
}

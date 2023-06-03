<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Resources;

use MoonShine\MoonShineRequest;

final class UpdateFormRequest extends MoonShineRequest
{
    public function authorize(): bool
    {
        if (! in_array(
            'edit',
            $this->getResource()->getActiveActions(),
            true
        )) {
            return false;
        }

        return $this->getResource()->can('update');
    }

    public function rules(): array
    {
        return $this->getResource()->rules($this->getItemOrFail());
    }
}

<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Resources;

use MoonShine\MoonShineRequest;

final class ViewFormRequest extends MoonShineRequest
{
    public function authorize(): bool
    {
        if (! in_array('show', $this->getResource()->getActiveActions(), true)) {
            return false;
        }

        return $this->getResource()->can('view', $this->getItemOrFail(eager: true));
    }

    public function rules(): array
    {
        return [];
    }
}

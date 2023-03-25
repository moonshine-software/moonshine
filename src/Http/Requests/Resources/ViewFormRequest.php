<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Requests\Resources;

use Leeto\MoonShine\MoonShineRequest;

final class ViewFormRequest extends MoonShineRequest
{
    public function authorize(): bool
    {
        if (! in_array('show', $this->getResource()->getActiveActions(), true)) {
            return false;
        }

        return $this->getResource()->can('view', $this->getItemOrFail());
    }

    public function rules(): array
    {
        return [];
    }
}

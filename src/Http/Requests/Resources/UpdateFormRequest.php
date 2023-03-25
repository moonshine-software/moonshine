<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Requests\Resources;

use Leeto\MoonShine\MoonShineRequest;

final class UpdateFormRequest extends MoonShineRequest
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
        return $this->getResource()->rules($this->getItemOrFail());
    }
}

<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Requests\Resources;

use Leeto\MoonShine\MoonShineRequest;

final class UpdateFormRequest extends MoonShineRequest
{
    public function authorize(): bool
    {
        return $this->getResource()->can('update', $this->getDataOrFail());
    }

    public function rules(): array
    {
        return $this->getResource()->rules($this->getDataOrFail());
    }
}

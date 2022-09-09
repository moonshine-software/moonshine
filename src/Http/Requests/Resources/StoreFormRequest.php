<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Requests\Resources;

use Leeto\MoonShine\MoonShineRequest;

final class StoreFormRequest extends MoonShineRequest
{
    public function authorize(): bool
    {
        return $this->getResource()->can('create', $this->getDataInstance());
    }

    public function rules(): array
    {
        return $this->getResource()->rules($this->getDataInstance());
    }
}

<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Requests;

use Leeto\MoonShine\MoonShineRequest;

class CreateFormRequest extends MoonShineRequest
{
    public function authorize(): bool
    {
        return $this->getResource()->can('create', $this->getModel());
    }

    public function rules(): array
    {
        return $this->getResource()->rules($this->getId() ? $this->findModel() : $this->getModel());
    }
}

<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Requests;

use Leeto\MoonShine\MoonShineRequest;

class DeleteFormRequest extends MoonShineRequest
{
    public function authorize(): bool
    {
        return $this->getResource()->can('delete', $this->findModel());
    }

    public function rules(): array
    {
        return [];
    }
}

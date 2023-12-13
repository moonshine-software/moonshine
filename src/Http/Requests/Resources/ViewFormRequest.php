<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Resources;

use MoonShine\Http\Requests\MoonShineFormRequest;

final class ViewFormRequest extends MoonShineFormRequest
{
    public function authorize(): bool
    {
        $this->beforeResourceAuthorization();

        if (! in_array(
            'view',
            $this->getResource()->getActiveActions(),
            true
        )) {
            return false;
        }

        return $this->getResource()->can('view');
    }

    public function rules(): array
    {
        return [];
    }
}

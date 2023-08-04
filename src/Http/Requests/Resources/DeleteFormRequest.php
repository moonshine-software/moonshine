<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Resources;

use MoonShine\Http\Requests\MoonshineFormRequest;

final class DeleteFormRequest extends MoonshineFormRequest
{
    public function authorize(): bool
    {
        if (! in_array(
            'delete',
            $this->getResource()->getActiveActions(),
            true
        )) {
            return false;
        }

        return $this->getResource()->can('delete');
    }
}

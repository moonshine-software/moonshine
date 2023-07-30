<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Resources;

use MoonShine\Http\Requests\MoonshineFormRequest;

final class PermissionFormFormRequest extends MoonshineFormRequest
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

    /**
     * @return array{permissions: string[]}
     */
    public function rules(): array
    {
        return [
            'permissions' => ['array'],
        ];
    }
}

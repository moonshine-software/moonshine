<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Resources;

use MoonShine\Http\Requests\MoonshineFormRequest;

final class UpdateFormRequest extends MoonshineFormRequest
{
    protected $errorBag = 'crud';

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

    public function rules(): array
    {
        return $this->getResource()->rules(
            $this->getResource()->getItemOrFail()
        );
    }
}

<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Resources;

use MoonShine\Fields\Field;
use MoonShine\MoonShineRequest;

final class UpdateColumnFormRequest extends MoonShineRequest
{
    public function authorize(): bool
    {
        if (! in_array('edit', $this->getResource()->getActiveActions(), true)) {
            return false;
        }

        if (!$this->field()) {
            return false;
        }

        return $this->getResource()->can('update');
    }

    public function rules(): array
    {
        return [
            'field' => ['required'],
            'value' => ['required']
        ];
    }

    public function field(): ?Field
    {
        return $this->getResource()
            ->getField(request('field', ''));
    }
}

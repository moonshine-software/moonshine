<?php

namespace MoonShine\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use MoonShine\Components\MoonshineComponent;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Decorations\Fragment;
use MoonShine\Pages\Page;

class MoonshineFormRequest extends FormRequest
{
    protected $errorBag = 'crud';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function messages(): array
    {
        if ($this->hasResource()) {
            return array_merge(
                trans('moonshine::validation'),
                $this->getResource()?->validationMessages() ?? []
            );
        }

        return parent::messages();
    }

    public function getResource(): ?ResourceContract
    {
        return moonshineRequest()->getResource();
    }

    public function hasResource(): bool
    {
        return ! is_null($this->getResource());
    }

    public function getPage(): Page
    {
        return moonshineRequest()->getPage();
    }

    //TODO replace return after decoration refactoring
    public function getPageComponent(string $name): null|MoonshineComponent|Fragment
    {
        return moonshineRequest()->getPageComponent($name);
    }

    public function redirectRoute(string $default): RedirectResponse
    {
        return redirect($default);
    }
}

<?php

namespace Leeto\MoonShine\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth(config('moonshine.auth.guard'))->guest();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'email' => (string)str(request('email'))
                ->lower()
                ->trim(),
        ]);
    }
}

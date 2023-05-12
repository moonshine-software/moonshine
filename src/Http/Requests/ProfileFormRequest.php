<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests;

use Illuminate\Validation\Rule;
use MoonShine\MoonShineAuth;
use MoonShine\MoonShineRequest;

class ProfileFormRequest extends MoonShineRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return MoonShineAuth::guard()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'username' => [
                'required',
                Rule::unique(
                    MoonShineAuth::model()?->getTable(),
                    config('moonshine.auth.fields.username', 'email')
                )->ignore(MoonShineAuth::guard()->id()),
            ],
            'avatar' => ['image'],
            'password' => 'sometimes|nullable|min:6|required_with:password_repeat|same:password_repeat',
        ];
    }
}

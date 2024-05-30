<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use MoonShine\Laravel\MoonShineAuth;

class ProfileFormRequest extends MoonShineFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return MoonShineAuth::guard()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array{name: string[], username: Unique[]|string[], avatar: string[], password: string}
     */
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'username' => [
                'required',
                Rule::unique(
                    MoonShineAuth::model()?->getTable(),
                    moonshineConfig()->getUserField('username', 'email')
                )->ignore(MoonShineAuth::guard()->id()),
            ],
            'avatar' => ['image'],
            'password' => 'sometimes|nullable|min:6|required_with:password_repeat|same:password_repeat',
        ];
    }
}

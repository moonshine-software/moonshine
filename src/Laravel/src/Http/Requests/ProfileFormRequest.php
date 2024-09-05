<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Requests;

use Illuminate\Validation\Rule;
use MoonShine\Laravel\MoonShineAuth;

class ProfileFormRequest extends MoonShineFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return MoonShineAuth::getGuard()->check();
    }

    public function rules(): array
    {
        return [
            moonshineConfig()->getUserField('name') => ['required'],
            moonshineConfig()->getUserField('username') => [
                'required',
                Rule::unique(
                    MoonShineAuth::getModel()?->getTable(),
                    moonshineConfig()->getUserField('username')
                )->ignore(MoonShineAuth::getGuard()->id()),
            ],
            moonshineConfig()->getUserField('avatar') => ['image'],
            moonshineConfig()->getUserField('password') => 'sometimes|nullable|min:6|required_with:password_repeat|same:password_repeat',
        ];
    }
}

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
        $name = moonshineConfig()->getUserField('name');
        $username = moonshineConfig()->getUserField('username');
        $avatar = moonshineConfig()->getUserField('avatar');
        $password = moonshineConfig()->getUserField('password');

        return array_filter([
            $name => blank($name) ? null : ['required'],
            $username => blank($username) ? null : [
                'required',
                Rule::unique(
                    MoonShineAuth::getModel()::class,
                    moonshineConfig()->getUserField('username')
                )->ignore(MoonShineAuth::getGuard()->id()),
            ],
            $avatar => blank($avatar) ? null : ['sometimes', 'nullable', 'image', 'mimes:jpeg,jpg,png,gif'],
            $password => blank($password) ? null : 'sometimes|nullable|min:6|required_with:password_repeat|same:password_repeat',
        ]);
    }
}

<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use function auth;
use function back;

use Illuminate\Http\RedirectResponse;

use Illuminate\Routing\Controller as BaseController;

use Illuminate\Support\Facades\Hash;
use MoonShine\Http\Requests\ProfileFormRequest;

class ProfileController extends BaseController
{
    public function store(ProfileFormRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (isset($data['password']) && $data['password'] !== '') {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('moonshine_users');
        }

        auth(config('moonshine.auth.guard'))
            ->user()
            ->update($data);

        return back();
    }
}

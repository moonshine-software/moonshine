<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Hash;
use Leeto\MoonShine\Http\Requests\LoginFormRequest;

use Leeto\MoonShine\Http\Requests\ProfileFormRequest;

use function auth;
use function back;
use function redirect;
use function trans;
use function view;

class MoonShineProfileController extends BaseController
{
    public function store(ProfileFormRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if(isset($data['password']) && $data['password'] !== '') {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('moonshine_users');
        }

        auth(config('moonshine.auth.guard'))
            ->user()
            ->update($data);

        return back();
    }
}

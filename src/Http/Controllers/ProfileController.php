<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Image;
use MoonShine\Http\Requests\ProfileFormRequest;
use MoonShine\Pages\Page;
use MoonShine\Pages\ProfilePage;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends MoonShineController
{
    public function store(ProfileFormRequest $request): Response
    {
        /** @var Page $page */
        $page = new (config('moonshine.pages.profile', ProfilePage::class))();
        $fields = Fields::make($page->fields());

        $image = $fields
            ->onlyFields()
            ->findByClass(Image::class);

        $data = $request->validated();
        $resultData = [
            config(
                'moonshine.auth.fields.username',
                'email'
            ) => $data['username'],
            config('moonshine.auth.fields.name', 'name') => $data['name'],
        ];

        if (isset($data['password']) && filled($data['password'])) {
            $resultData[config(
                'moonshine.auth.fields.password',
                'password'
            )] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if ($image && $request->hasFile('avatar')) {
            $resultData[config(
                'moonshine.auth.fields.avatar',
                'avatar'
            )] = $image->store($request->file('avatar'));
        } else {
            $resultData[config(
                'moonshine.auth.fields.avatar',
                'avatar'
            )] = $request->get('hidden_avatar');
        }

        $resultData = array_filter(
            $resultData,
            static fn ($key): bool => $key !== 0 && $key !== '',
            ARRAY_FILTER_USE_KEY
        );

        $request->user()->update($resultData);

        if ($request->ajax()) {
            return $this->json(message: __('moonshine::ui.saved'));
        }

        $this->toast(
            __('moonshine::ui.saved'),
            'success'
        );

        return back();
    }
}

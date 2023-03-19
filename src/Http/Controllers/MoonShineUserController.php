<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Leeto\MoonShine\Models\MoonshineUser;
use Leeto\MoonShine\Models\MoonshineUserPermission;
use Leeto\MoonShine\MoonShine;
use Leeto\MoonShine\Resources\MoonShineUserResource;
use Symfony\Component\HttpFoundation\Response;

class MoonShineUserController extends MoonShineCrudController
{
    public function __construct()
    {
        $resourceClass = (string) str(MoonShine::namespace('\Resources\\'))
            ->append('MoonShineUserResource');

        $this->resource = class_exists($resourceClass)
            ? new $resourceClass()
            : new MoonShineUserResource();
    }

    public function permissions(MoonshineUser $moonShineUser): RedirectResponse
    {
        abort_if(! $this->resource->can('update', $moonShineUser), Response::HTTP_FORBIDDEN);

        if (! request()->has('permissions')) {
            $moonShineUser->moonshineUserPermission()->delete();
        } else {
            MoonshineUserPermission::query()->updateOrCreate(
                ['moonshine_user_id' => $moonShineUser->id],
                request()
                    ->merge(['moonshine_user_id' => $moonShineUser->id])
                    ->only(['moonshine_user_id', 'permissions'])
            );
        }

        return back()
            ->with('alert', trans('moonshine::ui.saved'));
    }
}

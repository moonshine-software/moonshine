<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;
use MoonShine\Http\Requests\Resources\PermissionFormRequest;
use MoonShine\Models\MoonshineUserPermission;

class PermissionController extends BaseController
{
    public function __invoke(PermissionFormRequest $request): RedirectResponse
    {
        $item = $request->getItem();

        if (! $request->has('permissions')) {
            $item->moonshineUserPermission()->delete();
        } else {
            MoonshineUserPermission::query()->updateOrCreate(
                ['moonshine_user_id' => $item->getKey()],
                request()
                    ->merge(['moonshine_user_id' => $item->getKey()])
                    ->only(['moonshine_user_id', 'permissions'])
            );
        }

        return back()
            ->with('alert', trans('moonshine::ui.saved'));
    }
}

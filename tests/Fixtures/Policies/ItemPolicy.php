<?php

namespace MoonShine\Tests\Fixtures\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Tests\Fixtures\Models\Item;

class ItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(MoonshineUser $user)
    {
        if ($user->name === 'Policies test') {
            return false;
        }

        return true;
    }

    public function view(MoonshineUser $user, Item $item)
    {
        return false;
    }

    public function create(MoonshineUser $user)
    {
        return true;
    }

    public function update(MoonshineUser $user, Item $item)
    {
        if ($user->name === 'Policies test') {
            return false;
        }

        return true;
    }

    public function delete(MoonshineUser $user, Item $item)
    {
        return $user->moonshine_user_role_id === 1;
    }

    public function restore(MoonshineUser $user, Item $item)
    {
        return $user->moonshine_user_role_id === 1;
    }

    public function forceDelete(MoonshineUser $user, Item $item)
    {
        return $user->moonshine_user_role_id === 1;
    }

    public function massDelete(MoonshineUser $user)
    {
        return $user->moonshine_user_role_id === 1;
    }
}

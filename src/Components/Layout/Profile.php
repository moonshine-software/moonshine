<?php

declare(strict_types=1);

namespace MoonShine\Components\Layout;

use Illuminate\Support\Facades\Storage;
use MoonShine\Components\MoonshineComponent;
use MoonShine\Resources\MoonShineProfileResource;

/**
 * @method static static make()
 */
final class Profile extends MoonshineComponent
{
    protected string $view = 'moonshine::components.layout.profile';

    public function __construct(protected bool $withBorder = false)
    {
    }

    public function isWithBorder(): bool
    {
        return $this->withBorder;
    }

    protected function viewData(): array
    {
        $user = auth()->user();

        $avatar = $user?->{config('moonshine.auth.fields.avatar', 'avatar')};
        $nameOfUser = $user?->{config('moonshine.auth.fields.name', 'name')};
        $username = $user?->{config('moonshine.auth.fields.username', 'email')};

        $avatar = $avatar
            ? Storage::disk(config('moonshine.disk', 'public'))
                ->url($avatar)
            : "https://ui-avatars.com/api/?name=$nameOfUser";

        return [
            'route' => to_page(MoonShineProfileResource::class),
            'avatar' => $avatar,
            'nameOfUser' => $nameOfUser,
            'username' => $username,
            'withBorder' => $this->isWithBorder(),
        ];
    }
}

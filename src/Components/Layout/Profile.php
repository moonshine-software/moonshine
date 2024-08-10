<?php

declare(strict_types=1);

namespace MoonShine\Components\Layout;

use Closure;
use Illuminate\Support\Facades\Storage;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Pages\ProfilePage;

/**
 * @method static static make(?string $route = null, ?string $logOutRoute = null, ?string $avatar = null, ?\Closure|string|null $nameOfUser = null, ?\Closure|string|null $username = null, bool $withBorder = false)
 */
final class Profile extends MoonShineComponent
{
    protected string $view = 'moonshine::components.layout.profile';

    protected string $defaultAvatar = '';

    public function __construct(
        protected ?string $route = null,
        protected ?string $logOutRoute = null,
        protected ?string $avatar = null,
        protected Closure|string|null $nameOfUser = null,
        protected Closure|string|null $username = null,
        protected bool $withBorder = false,
    ) {
        $this->defaultAvatar = asset('vendor/moonshine/avatar.jpg');
    }

    public function isWithBorder(): bool
    {
        return $this->withBorder;
    }

    public function defaultAvatar(string $url): self
    {
        $this->defaultAvatar = $url;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        $user = auth()->user();

        $avatar = $user?->{config('moonshine.auth.fields.avatar', 'avatar')};
        $nameOfUser = $user?->{config('moonshine.auth.fields.name', 'name')};
        $username = $user?->{config('moonshine.auth.fields.username', 'email')};

        $avatar = $avatar
            ? Storage::disk(config('moonshine.disk', 'public'))
                ->url($avatar)
            : $this->defaultAvatar;

        return [
            'route' => $this->route ?? to_page(config('moonshine.pages.profile', ProfilePage::class)),
            'logOutRoute' => $this->logOutRoute ?? moonshineRouter()->to('logout'),
            'avatar' => $this->avatar ?? $avatar,
            'nameOfUser' => value($this->nameOfUser) ?? $nameOfUser,
            'username' => value($this->username) ?? $username,
            'withBorder' => $this->isWithBorder(),
        ];
    }
}

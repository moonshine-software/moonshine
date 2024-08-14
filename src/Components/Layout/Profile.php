<?php

declare(strict_types=1);

namespace MoonShine\Components\Layout;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Storage;
use MoonShine\Components\MoonShineComponent;
use MoonShine\MoonShineAuth;
use MoonShine\Pages\ProfilePage;

/**
 * @method static static make(?string $route = null, ?string $logOutRoute = null, Closure|string|null|false $avatar = null, Closure|string|null $nameOfUser = null, Closure|string|null $username = null, bool $withBorder = false)
 */
final class Profile extends MoonShineComponent
{
    protected string $view = 'moonshine::components.layout.profile';

    protected string $defaultAvatar = '';

    protected Authenticatable $user;

    public function __construct(
        protected ?string $route = null,
        protected ?string $logOutRoute = null,
        protected Closure|string|null|false $avatar = null,
        protected Closure|string|null $nameOfUser = null,
        protected Closure|string|null $username = null,
        protected bool $withBorder = false,
    ) {
        $this->user = MoonShineAuth::guard()->user();
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
        return [
            'route' => $this->route ?? to_page(config('moonshine.pages.profile', ProfilePage::class)),
            'logOutRoute' => $this->logOutRoute ?? moonshineRouter()->to('logout'),
            'avatar' => value($this->avatar, $this) ?? $this->getDefaultAvatar(),
            'nameOfUser' => value($this->nameOfUser, $this) ?? $this->getDefaultName(),
            'username' => value($this->username, $this) ?? $this->getDefaultUsername(),
            'withBorder' => $this->isWithBorder(),
        ];
    }

    private function getDefaultName(): string
    {
        return $this->user?->{config('moonshine.auth.fields.name', 'name')} ?? '';
    }

    private function getDefaultUsername(): string
    {
        return $this->user?->{config('moonshine.auth.fields.username', 'email')} ?? '';
    }

    private function getDefaultAvatar(): false|string
    {
        $avatar = $this->user?->{config('moonshine.auth.fields.avatar', 'avatar')};

        return $avatar
            ? Storage::disk(config('moonshine.disk', 'public'))->url($avatar)
            : $this->defaultAvatar;
    }
}

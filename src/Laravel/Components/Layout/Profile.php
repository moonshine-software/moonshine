<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Components\Layout;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Storage;
use MoonShine\Laravel\MoonShineAuth;
use MoonShine\Laravel\Pages\ProfilePage;
use MoonShine\UI\Components\MoonShineComponent;
use Throwable;

/**
 * @method static static make(?string $route = null, ?string $logOutRoute = null, ?Closure $avatar = null, ?Closure $nameOfUser = null, ?Closure $username = null, bool $withBorder = false)
 */
final class Profile extends MoonShineComponent
{
    protected string $view = 'moonshine::components.layout.profile';

    protected ?string $defaultAvatar = null;

    private ?Authenticatable $user;

    public function __construct(
        protected ?string $route = null,
        protected ?string $logOutRoute = null,
        protected ?Closure $avatar = null,
        protected ?Closure $nameOfUser = null,
        protected ?Closure $username = null,
        protected bool $withBorder = false,
    ) {
        parent::__construct();

        $this->user = MoonShineAuth::getGuard()->user();
    }

    public function isWithBorder(): bool
    {
        return $this->withBorder;
    }

    public function avatarPlaceholder(string $url): self
    {
        $this->defaultAvatar = $url;

        return $this;
    }

    public function getAvatarPlaceholder(): string
    {
        return $this->defaultAvatar ?? moonshineAssets()->getAsset('vendor/moonshine/avatar.jpg');
    }

    /**
     * @return array<string, mixed>
     * @throws Throwable
     */
    protected function viewData(): array
    {
        $nameOfUser = !is_null($this->nameOfUser)
            ? value($this->nameOfUser, $this)
            : $this->getDefaultName();

        $username = !is_null($this->username)
            ? value($this->username, $this)
            : $this->getDefaultUsername();

        $avatar = !is_null($this->avatar)
            ? value($this->avatar, $this)
            : $this->getDefaultAvatar();

        return [
            'route' => $this->route ?? toPage(
                moonshineConfig()->getPage('profile', ProfilePage::class)
            ),
            'logOutRoute' => $this->logOutRoute ?? moonshineRouter()->to('logout'),
            'avatar' => $avatar,
            'nameOfUser' => $nameOfUser,
            'username' => $username,
            'withBorder' => $this->isWithBorder(),
        ];
    }

    private function getDefaultName(): string
    {
        return $this->user?->{moonshineConfig()->getUserField('name')} ?? '';
    }

    private function getDefaultUsername(): string
    {
        return $this->user?->{moonshineConfig()->getUserField('username', 'email')} ?? '';
    }

    private function getDefaultAvatar(): false|string
    {
        $avatar = $this->user?->{moonshineConfig()->getUserField('avatar')};

        return $avatar
            ? Storage::disk(moonshineConfig()->getDisk())->url($avatar)
            : $this->getAvatarPlaceholder();
    }
}

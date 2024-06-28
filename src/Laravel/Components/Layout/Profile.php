<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Components\Layout;

use Illuminate\Support\Facades\Storage;
use MoonShine\Laravel\Pages\ProfilePage;
use MoonShine\UI\Components\MoonShineComponent;
use Throwable;

/**
 * @method static static make(?string $route = null, ?string $logOutRoute = null, ?string $avatar = null, ?string $nameOfUser = null, ?string $username = null, bool $withBorder = false)
 */
final class Profile extends MoonShineComponent
{
    protected string $view = 'moonshine::components.layout.profile';

    protected ?string $defaultAvatar = null;

    public function __construct(
        protected ?string $route = null,
        protected ?string $logOutRoute = null,
        protected ?string $avatar = null,
        protected ?string $nameOfUser = null,
        protected ?string $username = null,
        protected bool $withBorder = false,
    ) {
        parent::__construct();
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

    public function getDefaultAvatar(): string
    {
        return $this->defaultAvatar ?? moonshineAssets()->getAsset('vendor/moonshine/avatar.jpg');
    }

    /**
     * @return array<string, mixed>
     * @throws Throwable
     */
    protected function viewData(): array
    {
        $user = auth()->user();

        $avatar = $user?->{moonshineConfig()->getUserField('avatar')};
        $nameOfUser = $user?->{moonshineConfig()->getUserField('name')};
        $username = $user?->{moonshineConfig()->getUserField('username', 'email')};

        $avatar = $avatar
            ? Storage::disk(moonshineConfig()->getDisk())
                ->url($avatar)
            : $this->getDefaultAvatar();

        return [
            'route' => $this->route ?? toPage(
                moonshineConfig()->getPage('profile', ProfilePage::class)
            ),
            'logOutRoute' => $this->logOutRoute ?? moonshineRouter()->to('logout'),
            'avatar' => $this->avatar ?? $avatar,
            'nameOfUser' => $this->nameOfUser ?? $nameOfUser,
            'username' => $this->username ?? $username,
            'withBorder' => $this->isWithBorder(),
        ];
    }
}

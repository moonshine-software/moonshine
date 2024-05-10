<?php

declare(strict_types=1);

namespace MoonShine\Components\Layout;

use Illuminate\Support\Facades\Storage;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Pages\ProfilePage;

/**
 * @method static static make(?string $route = null, ?string $logOutRoute = null, ?string $avatar = null, ?string $nameOfUser = null, ?string $username = null, bool $withBorder = false)
 */
final class Profile extends MoonShineComponent
{
    protected string $view = 'moonshine::components.layout.profile';

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

    /**
     * @return array<string, mixed>
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
            : "https://ui-avatars.com/api/?name=$nameOfUser";

        return [
            'route' => $this->route ?? to_page(
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

<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Components;

use Illuminate\Support\Collection;
use MoonShine\UI\Components\MoonShineComponent;

/**
 * @method static static make(bool $profileMode = false)
 */
final class SocialAuth extends MoonShineComponent
{
    protected string $view = 'moonshine::components.social-auth';

    public Collection $attached;

    public array $drivers;

    protected array $translates = [
        'linked' => 'moonshine::ui.resource.linked_socialite'
    ];

    public function __construct(
        public bool $profileMode = false
    ) {
        parent::__construct();

        $this->drivers = collect(moonshineConfig()->getSocialite())
            ->map(fn(string $name, string $src): array => [
                'name' => $name,
                'src' => moonshineAssets()->getAsset($src),
                'route' => moonshineRouter()->to('socialite.redirect', [
                    'driver' => $name
                ]),
            ])
            ->toArray();
        $this->attached = auth()->user()?->moonshineSocialites ?? collect();
    }
}

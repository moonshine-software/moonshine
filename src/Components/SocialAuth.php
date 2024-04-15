<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Illuminate\Support\Collection;

/**
 * @method static static make(bool $profileMode = false)
 */
final class SocialAuth extends MoonShineComponent
{
    protected string $view = 'moonshine::components.social-auth';

    public Collection $attached;

    public array $drivers;

    public function __construct(
        public bool $profileMode = false
    ) {
        $this->drivers = config('moonshine.socialite');
        $this->attached = auth()->user()?->moonshineSocialites ?? collect();
    }
}

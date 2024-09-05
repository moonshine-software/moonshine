<?php

declare(strict_types=1);

namespace MoonShine\AssetManager\Contracts;

interface HasLinkContact
{
    public function getLink(): string;

    public function link(string $link): static;
}

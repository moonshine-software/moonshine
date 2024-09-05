<?php

declare(strict_types=1);

namespace MoonShine\AssetManager;

use MoonShine\AssetManager\Contracts\HasLinkContact;
use MoonShine\AssetManager\Contracts\HasVersionContact;
use MoonShine\AssetManager\Traits\HasLink;
use MoonShine\AssetManager\Traits\WithVersion;
use MoonShine\Contracts\AssetManager\AssetElementContract;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\Support\Traits\Makeable;
use MoonShine\Support\Traits\WithComponentAttributes;

/**
 * @method static static make(string $link)
 */
final class Css implements AssetElementContract, HasLinkContact, HasVersionContact
{
    use Makeable;
    use WithVersion;
    use HasLink;
    use WithComponentAttributes;

    public function __construct(
        string $link,
    ) {
        $this->link = $link;
        $this->attributes = new MoonShineComponentAttributeBag();
    }

    public function defer(): self
    {
        $this->customAttributes([
            'defer' => '',
        ]);

        return $this;
    }

    public function toHtml(): string
    {
        $this->customAttributes([
            'href' => $this->getLink(),
            'rel' => 'stylesheet',
        ]);

        return <<<HTML
            <link {$this->getAttributes()}>
        HTML;
    }

    public function __toString(): string
    {
        return $this->getLink();
    }
}

<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Fields;

use Illuminate\Support\Collection;
use Leeto\MoonShine\InputExtensions\InputCopy;
use Leeto\MoonShine\InputExtensions\InputExt;
use Leeto\MoonShine\InputExtensions\InputExtension;
use Leeto\MoonShine\InputExtensions\InputEye;
use Leeto\MoonShine\InputExtensions\InputLock;

trait WithInputExtensions
{
    protected array $extensions = [];

    public function extension(InputExtension $extension): static
    {
        $this->extensions[$extension::class] = $extension;
        $this->setAttribute('x-ref', 'extensionInput');

        return $this;
    }

    public function getExtensions(): Collection
    {
        return collect($this->extensions);
    }


    /** Just a sugar methods below */
    public function copy(): static
    {
        $this->extension(new InputCopy());

        return $this;
    }

    public function eye(): static
    {
        $this->extension(new InputEye());

        return $this;
    }

    public function locked(): static
    {
        $this->extension(new InputLock());

        return $this;
    }

    public function expansion(string $ext): static
    {
        $this->extension(new InputExt($ext));

        return $this;
    }
}

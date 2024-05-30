<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use Illuminate\Support\Collection;
use MoonShine\UI\InputExtensions\InputCopy;
use MoonShine\UI\InputExtensions\InputExt;
use MoonShine\UI\InputExtensions\InputExtension;
use MoonShine\UI\InputExtensions\InputEye;
use MoonShine\UI\InputExtensions\InputLock;

trait WithInputExtensions
{
    protected array $extensions = [];

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

    public function extension(InputExtension $extension): static
    {
        $this->extensions[$extension::class] = $extension;
        $this->setAttribute('x-ref', 'extensionInput');

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

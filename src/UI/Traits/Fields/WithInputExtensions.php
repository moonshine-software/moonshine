<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use Illuminate\Support\Collection;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
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

    public function getExtensionsAttributes(): MoonShineComponentAttributeBag
    {
        $extensions = $this->getExtensions();

        return new MoonShineComponentAttributeBag([
            'x-init' => trim($extensions->implode(fn($extension) => $extension->getXInit()->implode(';'), ';'), ';'),
            'x-data' => str(
                $extensions->implode(fn($extension) => $extension->getXData()->implode(','), ','),
            )->trim(',')->wrap('{', '}'),
        ]);
    }

    protected function getExtensionsViewData(): array
    {
        return [
            'extensions' => $this->getExtensions(),
            'extensionsAttributes' => $this->getExtensionsAttributes(),
        ];
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

    public function suffix(string $ext): static
    {
        $this->extension(new InputExt($ext));

        return $this;
    }
}

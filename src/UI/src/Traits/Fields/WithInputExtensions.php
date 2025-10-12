<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use MoonShine\Contracts\UI\ComponentAttributesBagContract;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\UI\InputExtensions\InputCopy;
use MoonShine\UI\InputExtensions\InputExt;
use MoonShine\UI\InputExtensions\InputExtension;
use MoonShine\UI\InputExtensions\InputEye;
use MoonShine\UI\InputExtensions\InputLock;
use MoonShine\UI\InputExtensions\InputPrefix;

trait WithInputExtensions
{
    /**
     * @var list<InputExtension>
     */
    protected array $extensions = [];

    /**
     * @return Collection<array-key, InputExtension>
     */
    public function getExtensions(): Collection
    {
        return new Collection($this->extensions);
    }

    public function getExtensionsAttributes(): ComponentAttributesBagContract
    {
        $extensions = $this->getExtensions();

        return new MoonShineComponentAttributeBag([
            'x-init' => trim($extensions->implode(static fn (InputExtension $extension): string => $extension->getXInit()->implode(';'), ';'), ';'),
            'x-data' => Str::of(
                $extensions->implode(static fn (InputExtension $extension): string => $extension->getXData()->implode(','), ','),
            )->trim(',')->wrap('{', '}'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getExtensionsViewData(): array
    {
        return [
            'extensions' => $this->getExtensions(),
            'extensionsAttributes' => $this->getExtensionsAttributes(),
        ];
    }

    /** Just a sugar methods below */
    public function copy(string $value = '{{value}}'): static
    {
        $this->extension(new InputCopy($value));

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

    public function prefix(string $ext): static
    {
        $this->extension(new InputPrefix($ext));

        return $this;
    }

    public function suffix(string $ext): static
    {
        $this->extension(new InputExt($ext));

        return $this;
    }
}

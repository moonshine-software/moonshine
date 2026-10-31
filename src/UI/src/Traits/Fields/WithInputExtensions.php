<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use MoonShine\Contracts\UI\ComponentAttributesBagContract;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\UI\Components\Snippet;
use MoonShine\UI\Enums\DisplayTextType;
use MoonShine\UI\InputExtensions\InputCopy;
use MoonShine\UI\InputExtensions\InputExt;
use MoonShine\UI\InputExtensions\InputExtension;
use MoonShine\UI\InputExtensions\InputEye;
use MoonShine\UI\InputExtensions\InputLock;
use MoonShine\UI\InputExtensions\InputPrefix;
use MoonShine\UI\InputExtensions\InputTextExtension;
use MoonShine\UI\Traits\WithDisplayEscaping;

trait WithInputExtensions
{
    use WithDisplayEscaping;

    public function escapePrefix(bool $escape = true): static
    {
        return $this->setDisplayEscaping(DisplayTextType::PREFIX, $escape);
    }

    public function unescapePrefix(): static
    {
        return $this->escapePrefix(false);
    }

    public function isEscapePrefix(): bool
    {
        return $this->isEscapeExtensionText(DisplayTextType::PREFIX, InputPrefix::class);
    }

    public function escapeSuffix(bool $escape = true): static
    {
        return $this->setDisplayEscaping(DisplayTextType::SUFFIX, $escape);
    }

    public function unescapeSuffix(): static
    {
        return $this->escapeSuffix(false);
    }

    public function isEscapeSuffix(): bool
    {
        return $this->isEscapeExtensionText(DisplayTextType::SUFFIX, InputExt::class);
    }

    /**
     * The field preference wins, then an explicit extension preference, then the global default.
     *
     * @param class-string<InputTextExtension> $extension
     */
    private function isEscapeExtensionText(DisplayTextType $type, string $extension): bool
    {
        $extension = $this->extensions[$extension] ?? null;

        return $this->getDisplayEscaping($type)
            ?? ($extension instanceof InputTextExtension ? $extension->isEscape() : $type->isEscapedBy($this->getCore()->getConfig()));
    }

    /**
     * @var array<class-string<InputExtension>, InputExtension>
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
        // Rendering uses clones, so shared extension instances keep their own preferences.
        return [
            'extensions' => $this->getExtensions()->map(fn (InputExtension $extension): InputExtension => match (true) {
                $extension instanceof InputPrefix => (clone $extension)->escape($this->isEscapePrefix()),
                $extension instanceof InputExt => (clone $extension)->escape($this->isEscapeSuffix()),
                default => $extension,
            }),
            'extensionsAttributes' => $this->getExtensionsAttributes(),
        ];
    }

    /** Just a sugar methods below */
    public function copy(string $value = '{{value}}'): static
    {
        $this->extension(new InputCopy($value));
        $this->changePreview(fn (mixed $value): string => $value ? (string) Snippet::make($this->stringifySlotContent($value)) : $this->stringifySlotContent($value));

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

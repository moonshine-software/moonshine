<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\Support\DTOs\FileItem;
use MoonShine\Support\Stringify;
use MoonShine\UI\Components\Boolean;
use MoonShine\UI\Components\Thumbnails;

class Preview extends Field
{
    protected string $view = 'moonshine::fields.preview';

    protected bool $isBoolean = false;

    protected bool $isImage = false;

    protected bool $hideTrue = false;

    protected bool $hideFalse = false;

    protected bool $hasOld = false;

    /**
     * @param bool|Closure(static): (bool|null)|null $hideTrue
     * @param bool|Closure(static): (bool|null)|null $hideFalse
     */
    public function boolean(
        mixed $hideTrue = null,
        mixed $hideFalse = null
    ): static {
        $this->hideTrue = value($hideTrue, $this) ?? false;
        $this->hideFalse = value($hideFalse, $this) ?? false;

        $this->isBoolean = true;

        return $this;
    }

    public function image(): static
    {
        $this->isImage = true;

        return $this;
    }

    protected function resolvePreview(): Renderable|string
    {
        $value = $this->toFormattedValue();

        if ($this->isBoolean) {
            $value = (bool) $value;

            return match (true) {
                $this->hideTrue && $value, $this->hideFalse && ! $value => '',
                default => (string) Boolean::make($value),
            };
        }

        if ($this->isImage) {
            /** @var FileItem|string|list<FileItem|string|array{full_path?: string|null, raw_value?: string|null, name?: string|null, attributes?: array<string, mixed>|MoonShineComponentAttributeBag|null}>|null $value */
            return (string) Thumbnails::make(
                $value
            );
        }

        return Stringify::value($value);
    }

    protected function prepareBeforeRender(): void
    {
        parent::prepareBeforeRender();

        $this->removeAttribute('name');
    }

    protected function resolveValue(): mixed
    {
        return $this->preview();
    }

    protected function resolveOnApply(): ?Closure
    {
        return static fn ($item) => $item;
    }

    public function isCanApply(): bool
    {
        return false;
    }
}

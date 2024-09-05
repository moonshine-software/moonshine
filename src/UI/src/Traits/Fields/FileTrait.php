<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use Closure;
use Illuminate\Support\Collection;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\UI\Traits\WithStorage;

trait FileTrait
{
    use WithStorage;

    protected array $allowedExtensions = [];

    protected bool $disableDownload = false;

    protected bool $keepOriginalFileName = false;

    protected ?Closure $customName = null;

    protected ?Closure $names = null;

    protected ?Closure $itemAttributes = null;

    protected ?Closure $remainingValuesResolver = null;

    protected ?Collection $remainingValues = null;

    /**
     * @param  Closure(string $filename, int $index): string  $callback
     */
    public function names(Closure $callback): static
    {
        $this->names = $callback;

        return $this;
    }

    public function resolveNames(): Closure
    {
        return function (string $filename, int $index = 0): string {
            if (is_null($this->names)) {
                return $filename;
            }

            return (string) value($this->names, $filename, $index);
        };
    }

    /**
     * @param  Closure(string $filename, int $index): string  $callback
     */
    public function itemAttributes(Closure $callback): static
    {
        $this->itemAttributes = $callback;

        return $this;
    }

    public function resolveItemAttributes(): Closure
    {
        return function (string $filename, int $index = 0): MoonShineComponentAttributeBag {
            if (is_null($this->itemAttributes)) {
                return new MoonShineComponentAttributeBag();
            }

            return new MoonShineComponentAttributeBag(
                (array) value($this->itemAttributes, $filename, $index)
            );
        };
    }

    public function keepOriginalFileName(): static
    {
        $this->keepOriginalFileName = true;

        return $this;
    }

    public function isKeepOriginalFileName(): bool
    {
        return $this->keepOriginalFileName;
    }

    public function customName(Closure $name): static
    {
        $this->customName = $name;

        return $this;
    }

    public function getCustomName(): ?Closure
    {
        return $this->customName;
    }

    public function allowedExtensions(array $allowedExtensions): static
    {
        $this->allowedExtensions = $allowedExtensions;

        if ($allowedExtensions !== []) {
            $this->setAttribute('accept', $this->getAcceptExtension());
        }

        return $this;
    }

    public function getAcceptExtension(): string
    {
        $extensions = array_map(
            static fn ($val): string => '.' . $val,
            $this->allowedExtensions
        );

        return implode(',', $extensions);
    }

    public function disableDownload(Closure|bool|null $condition = null): static
    {
        $this->disableDownload = value($condition, $this) ?? true;

        return $this;
    }

    public function canDownload(): bool
    {
        return ! $this->disableDownload;
    }

    public function getPathWithDir(string $value): string
    {
        return $this->getPath($this->getPrependedDir($value));
    }

    public function getPath(string $value): string
    {
        return $this->getStorageUrl($value);
    }

    public function getPrependedDir(string $value): string
    {
        $dir = empty($this->getDir()) ? '' : $this->getDir() . '/';

        return str($value)->remove($dir)
            ->prepend($dir)
            ->value();
    }

    public function getHiddenRemainingValuesKey(): string
    {
        $column = str($this->getColumn())->explode('.')->last();
        $hiddenColumn = str($this->getVirtualColumn())->explode('.')->last();

        return str($this->getRequestNameDot())
            ->replaceLast($column, "hidden_$hiddenColumn")
            ->value();
    }

    public function getHiddenRemainingValuesName(): string
    {
        $column = str($this->getColumn())->explode('.')->last();
        $hiddenColumn = str($this->getVirtualColumn())->explode('.')->last();

        return str($this->getNameAttribute())
            ->replaceLast($column, "hidden_$hiddenColumn")
            ->value();
    }

    /**
     * @param  Closure(static $ctx): Collection  $callback
     */
    public function remainingValuesResolver(Closure $callback): static
    {
        $this->remainingValuesResolver = $callback;

        return $this;
    }

    public function setRemainingValues(iterable $values): void
    {
        $this->remainingValues = collect($values);
    }

    public function getRemainingValues(): Collection
    {
        if (! is_null($this->remainingValues)) {
            $values = $this->remainingValues;

            $this->remainingValues = null;

            return $values;
        }


        if (! is_null($this->remainingValuesResolver)) {
            return value($this->remainingValuesResolver, $this);
        }

        return collect(
            $this->getCore()->getRequest()->get(
                $this->getHiddenRemainingValuesKey()
            )
        );
    }

    public function isAllowedExtension(string $extension): bool
    {
        return empty($this->getAllowedExtensions())
            || in_array($extension, $this->getAllowedExtensions(), true);
    }

    public function getAllowedExtensions(): array
    {
        return $this->allowedExtensions;
    }

    protected function resolveValue(): mixed
    {
        if ($this->isMultiple() && ! $this->toValue(false) instanceof Collection) {
            return collect($this->toValue(false));
        }

        return parent::resolveValue();
    }

    public function getFullPathValues(): array
    {
        $values = $this->toFormattedValue();

        if (! $values) {
            return [];
        }

        return $this->isMultiple()
            ? collect($values)
                ->map(fn ($value): string => $this->getPathWithDir($value))
                ->toArray()
            : [$this->getPathWithDir($values)];
    }

    public function removeExcludedFiles(): void
    {
        $values = collect(
            $this->toValue(withDefault: false)
        );

        $values->diff($this->getRemainingValues())->each(fn (string $file) => $this->deleteFile($file));
    }
}

<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Closure;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\ComponentAttributeBag;
use MoonShine\Exceptions\FieldException;
use MoonShine\Support\Condition;
use MoonShine\Traits\WithStorage;
use Throwable;

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

    public function names(Closure $closure): static
    {
        $this->names = $closure;

        return $this;
    }

    public function resolveNames(): Closure
    {
        return function (string $filename, int $index = 0): string {
            if(is_null($this->names)) {
                return $filename;
            }

            return (string) value($this->names, $filename, $index);
        };
    }

    public function itemAttributes(Closure $closure): static
    {
        $this->itemAttributes = $closure;

        return $this;
    }

    public function resolveItemAttributes(): Closure
    {
        return function (string $filename, int $index = 0): ComponentAttributeBag {
            if(is_null($this->itemAttributes)) {
                return new ComponentAttributeBag();
            }

            return new ComponentAttributeBag(
                (array) value($this->itemAttributes, $filename, $index)
            );
        };
    }

    public function keepOriginalFileName(): static
    {
        $this->keepOriginalFileName = true;

        return $this;
    }

    public function customName(Closure $name): static
    {
        $this->customName = $name;

        return $this;
    }

    public function allowedExtensions(array $allowedExtensions): static
    {
        $this->allowedExtensions = $allowedExtensions;

        if ($allowedExtensions !== []) {
            $this->setAttribute('accept', $this->acceptExtension());
        }

        return $this;
    }

    public function acceptExtension(): string
    {
        $extensions = array_map(
            static fn ($val): string => '.' . $val,
            $this->allowedExtensions
        );

        return implode(',', $extensions);
    }

    public function disableDownload(Closure|bool|null $condition = null): static
    {
        $this->disableDownload = Condition::boolean($condition, true);

        return $this;
    }

    public function canDownload(): bool
    {
        return ! $this->disableDownload;
    }

    public function pathWithDir(string $value): string
    {
        return $this->path($this->prependDir($value));
    }

    public function path(string $value): string
    {
        return Storage::disk($this->getDisk())->url($value);
    }

    public function prependDir(string $value): string
    {
        $dir = empty($this->getDir()) ? '' : $this->getDir() . '/';

        return str($value)->remove($dir)
            ->prepend($dir)
            ->value();
    }

    public function hiddenOldValuesKey(): string
    {
        $column = str($this->column())->explode('.')->last();
        $hiddenColumn = str($this->getVirtualColumn())->explode('.')->last();

        return str($this->nameDot())
            ->replaceLast($column, "hidden_$hiddenColumn")
            ->value();
    }

    public function hiddenOldValuesName(): string
    {
        $column = str($this->column())->explode('.')->last();
        $hiddenColumn = str($this->getVirtualColumn())->explode('.')->last();

        return str($this->name())
            ->replaceLast($column, "hidden_$hiddenColumn")
            ->value();
    }

    /**
     * @throws Throwable
     */
    public function store(UploadedFile $file): string
    {
        $extension = $file->extension();

        throw_if(
            ! $this->isAllowedExtension($extension),
            new FieldException("$extension not allowed")
        );

        if ($this->keepOriginalFileName) {
            return $file->storeAs(
                $this->getDir(),
                $file->getClientOriginalName(),
                $this->parseOptions()
            );
        }

        if (is_closure($this->customName)) {
            return $file->storeAs(
                $this->getDir(),
                value($this->customName, $file, $this),
                $this->parseOptions()
            );
        }

        if(! $result = $file->store($this->getDir(), $this->parseOptions())) {
            throw new FieldException('Failed to save file, check your permissions');
        }

        return $result;
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

    /**
     * @param  Closure(static $ctx): Collection  $closure
     */
    public function remainingValuesResolver(Closure $closure): static
    {
        $this->remainingValuesResolver = $closure;

        return $this;
    }

    public function setRemainingValues(iterable $values): void
    {
        $this->remainingValues = collect($values);
    }

    public function getRemainingValues(): Collection
    {
        if(! is_null($this->remainingValues)) {
            $values = $this->remainingValues;

            $this->remainingValues = null;

            return $values;
        }

        if(! is_null($this->remainingValuesResolver)) {
            return value($this->remainingValuesResolver, $this);
        }

        return request()->collect(
            $this->hiddenOldValuesKey()
        );
    }

    protected function resolveOnApply(): ?Closure
    {
        return function ($item) {
            $requestValue = $this->requestValue();
            $remainingValues = $this->getRemainingValues();

            data_forget($item, $this->hiddenOldValuesKey());

            $newValue = $this->isMultiple() ? $remainingValues : $remainingValues->first();

            if ($requestValue !== false) {
                if ($this->isMultiple()) {
                    $paths = [];

                    foreach ($requestValue as $file) {
                        $paths[] = $this->store($file);
                    }

                    $newValue = $newValue->merge($paths)
                        ->values()
                        ->unique()
                        ->toArray();
                } else {
                    $newValue = $this->store($requestValue);
                    $this->setRemainingValues([]);
                }
            }

            $this->removeExcludedFiles();

            return data_set($item, $this->column(), $newValue);
        };
    }

    public function removeExcludedFiles(): void
    {
        $values = collect(
            $this->toValue(withDefault: false)
        );

        $values->diff($this->getRemainingValues())->each(fn (string $file) => $this->deleteFile($file));
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
        $values = $this->toValue(withDefault: false);

        if (! $values) {
            return [];
        }

        return $this->isMultiple()
            ? collect($values)
                ->map(fn ($value): string => $this->pathWithDir($value))
                ->toArray()
            : [$this->pathWithDir($values)];
    }
}

<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use MoonShine\Contracts\UI\ComponentAttributesBagContract;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\Support\DTOs\FileItemExtra;
use MoonShine\UI\Contracts\FileableContract;
use MoonShine\UI\Traits\WithStorage;

trait FileTrait
{
    use WithStorage;

    /**
     * @var string[]
     */
    protected array $allowedExtensions = [];

    protected bool $disableDownload = false;

    protected bool $keepOriginalFileName = false;

    /** @var null|Closure(mixed, static): string */
    protected ?Closure $customName = null;

    /** @var null|Closure(string, int): string */
    protected ?Closure $names = null;

    /** @var null|Closure(string, int): array<string, mixed> */
    protected ?Closure $itemAttributes = null;

    /** @var null|Closure(string, int): ?FileItemExtra */
    protected ?Closure $extraAttributes = null;

    /** @var null|Closure(static): array<string, mixed> */
    protected ?Closure $dropzoneAttributes = null;

    /** @var null|Closure(static): Collection<array-key, mixed> */
    protected ?Closure $remainingValuesResolver = null;

    /** @var Collection<array-key, string>|null */
    protected ?Collection $remainingValues = null;

    public function dropzoneAttributes(Closure $attributes): static
    {
        $this->dropzoneAttributes = $attributes;

        return $this;
    }

    public function getDropzoneAttributes(): ComponentAttributesBagContract
    {
        $attributes = new MoonShineComponentAttributeBag(
            $this->dropzoneAttributes === null ? [] : \call_user_func($this->dropzoneAttributes, $this),
        );

        if (! $attributes->has('x-data')) {
            $attributes = $attributes->merge([
                'x-data' => 'sortable',
                'data-handle' => '.dropzone-item',
            ]);
        }

        return $attributes;
    }

    /**
     * @param  string|Closure(static): string  $url
     */
    public function reorderable(string|Closure $url, ?string $group = null): static
    {
        return $this->dropzoneAttributes(static function (FileableContract $ctx) use ($url, $group): array {
            $url = value($url, $ctx);

            return [
                'x-data' => "sortable(`$url`, `$group`)",
                'data-handle' => '.dropzone-item',
            ];
        });
    }

    /**
     * @param  Closure(string $filename, int $index): string  $callback
     */
    public function names(Closure $callback): static
    {
        $this->names = $callback;

        return $this;
    }

    /** @return Closure(string, int, static): string */
    public function resolveNames(): Closure
    {
        return function (string $filename, int $index = 0): string {
            if (\is_null($this->names)) {
                return $filename;
            }

            return \call_user_func($this->names, $filename, $index);
        };
    }

    /**
     * @param  Closure(string $filename, int $index): array<string, mixed>  $callback
     */
    public function itemAttributes(Closure $callback): static
    {
        $this->itemAttributes = $callback;

        return $this;
    }

    /**
     * @return Closure(string $filename, int $index, static): ComponentAttributesBagContract
     */
    public function resolveItemAttributes(): Closure
    {
        return function (string $filename, int $index = 0): ComponentAttributesBagContract {
            if (\is_null($this->itemAttributes)) {
                return new MoonShineComponentAttributeBag();
            }

            return new MoonShineComponentAttributeBag(
                (array)\call_user_func($this->itemAttributes, $filename, $index),
            );
        };
    }

    /**
     * @param  Closure(string $filename, int $index): ?FileItemExtra  $callback
     */
    public function extraAttributes(Closure $callback): static
    {
        $this->extraAttributes = $callback;

        return $this;
    }

    /**
     * @return Closure(string $filename, int $index, static): ?FileItemExtra
     */
    public function resolveExtraAttributes(): Closure
    {
        return function (string $filename, int $index = 0): ?FileItemExtra {
            if (\is_null($this->extraAttributes)) {
                return null;
            }

            return \call_user_func($this->extraAttributes, $filename, $index);
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

    /**
     * @param  Closure(mixed $file, static $ctx): string  $name
     */
    public function customName(Closure $name): static
    {
        $this->customName = $name;

        return $this;
    }

    /**
     * @return null|Closure(mixed $file, static $ctx): string
     */
    public function getCustomName(): ?Closure
    {
        return $this->customName;
    }

    /**
     * @param  string[]  $allowedExtensions
     */
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
            $this->allowedExtensions,
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

        return Str::of($value)
            ->remove($dir)
            ->prepend($dir)
            ->value();
    }

    public function getHiddenRemainingValuesKey(): string
    {
        $column = Str::of($this->getColumn())->explode('.')->last();

        return Str::of($this->getRequestNameDot())
            ->replaceLast($column, $this->getHiddenColumn())
            ->value();
    }

    public function getHiddenColumn(): string
    {
        $column = (string)Str::of($this->getVirtualColumn())->explode('.')->last();

        return "hidden_$column";
    }

    public function getHiddenRemainingValuesName(): string
    {
        $column = Str::of($this->getColumn())->explode('.')->last();

        return Str::of($this->getNameAttribute())
            ->replaceLast($column, $this->getHiddenColumn())
            ->value();
    }

    public function getHiddenAttributes(): ComponentAttributesBagContract
    {
        return $this->getAttributes()->only(['data-level'])->merge([
            'name' => $this->getHiddenRemainingValuesName(),
            'data-name' => $this->getHiddenRemainingValuesName(),
        ]);
    }

    /**
     * @param  Closure(static $ctx): Collection<array-key, null|string>  $callback
     */
    public function remainingValuesResolver(Closure $callback): static
    {
        $this->remainingValuesResolver = $callback;

        return $this;
    }

    /**
     * @param  iterable<array-key, string>  $values
     */
    public function setRemainingValues(iterable $values): void
    {
        $this->remainingValues = new Collection($values);
    }

    /** @return Collection<array-key, string> */
    public function getRemainingValues(): Collection
    {
        if (! \is_null($this->remainingValues)) {
            $values = $this->remainingValues;

            $this->remainingValues = null;

            return $values;
        }


        if (! \is_null($this->remainingValuesResolver)) {
            return \call_user_func($this->remainingValuesResolver, $this);
        }

        return new Collection(
            $this->getCore()->getRequest()->get(
                $this->getHiddenRemainingValuesKey(),
            ),
        );
    }

    public function isAllowedExtension(string $extension): bool
    {
        return empty($this->getAllowedExtensions())
               || \in_array($extension, $this->getAllowedExtensions(), true);
    }

    /**
     * @return string[]
     */
    public function getAllowedExtensions(): array
    {
        return $this->allowedExtensions;
    }

    protected function resolveValue(): mixed
    {
        if ($this->isMultiple() && ! $this->toValue(false) instanceof Collection) {
            return new Collection($this->toValue(false));
        }

        return parent::resolveValue();
    }

    /**
     * @return string[]
     */
    public function getFullPathValues(): array
    {
        $values = $this->toFormattedValue();

        if (! $values) {
            return [];
        }

        if ($this->isMultiple()) {
            $collection = new Collection($values);

            return $collection
                ->map(fn (string $value): string => $this->getPathWithDir($value))
                ->toArray();
        }

        return [$this->getPathWithDir($values)];
    }

    /**
     * @param  string[]|string|null  $newValue
     *
     * @return void
     */
    public function removeExcludedFiles(null|array|string $newValue = null): void
    {
        $values = new Collection(
            $this->toValue(withDefault: false),
        );

        $values->diff($this->getRemainingValues())->each(
            function (?string $file) use ($newValue): void {
                $old = array_filter(\is_array($newValue) ? $newValue : [$newValue]);

                if ($file !== null && ! \in_array($file, $old, true)) {
                    $this->deleteFile($file);
                }
            },
        );
    }
}

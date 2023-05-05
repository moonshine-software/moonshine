<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use MoonShine\Exceptions\FieldException;
use MoonShine\Helpers\Condition;
use MoonShine\Traits\WithStorage;
use Throwable;

trait FileTrait
{
    use WithStorage;

    protected array $allowedExtensions = [];

    protected bool $disableDownload = false;

    protected bool $keepOriginalFileName = false;

    /**
     * @deprecated Will be removed
     */
    protected string $withPrefix = '';

    public function keepOriginalFileName(): static
    {
        $this->keepOriginalFileName = true;

        return $this;
    }

    public function allowedExtensions(array $allowedExtensions): static
    {
        $this->allowedExtensions = $allowedExtensions;
        if (! empty($allowedExtensions)) {
            $this->setAttribute("accept", $this->acceptExtension());
        }

        return $this;
    }

    public function getAllowedExtensions(): array
    {
        return $this->allowedExtensions;
    }

    /**
     * @param  string  $extension
     * @return bool
     */
    public function isAllowedExtension(string $extension): bool
    {
        return empty($this->getAllowedExtensions())
            || in_array($extension, $this->getAllowedExtensions(), true);
    }

    public function disableDownload($condition = null): static
    {
        $this->disableDownload = Condition::boolean($condition, true);

        return $this;
    }

    public function canDownload(): bool
    {
        return ! $this->disableDownload;
    }

    public function path(string $value): string
    {
        return Storage::disk($this->getDisk())->url($value);
    }

    public function pathWithDir(string $value): string
    {
        $dir = ! (empty($this->getDir())) ? $this->getDir(). '/' : '';

        return $this->path(str($value)->remove($dir)
            ->prepend($dir)
            ->value());
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
                $this->getDisk()
            );
        }

        return $file->store($this->getDir(), $this->getDisk());
    }

    /**
     * @throws Throwable
     */
    public function hasManyOrOneSave($hiddenKey, array $values = []): array
    {
        if ($this->isMultiple()) {
            $saveValues = collect(request($hiddenKey, []))
                ->reject(fn ($v) => is_numeric($v));

            if (isset($values[$this->field()])) {
                foreach ($values[$this->field()] as $value) {
                    $saveValues = $saveValues->merge([
                        $this->store($value),
                    ]);
                }
            }

            $values[$this->field()] = $saveValues->values()
                ->filter()
                ->unique()
                ->toArray();
        } elseif (isset($values[$this->field()])) {
            $values[$this->field()] = $this->store($values[$this->field()]);
        } elseif (! isset($values[$this->field()])) {
            $values[$this->field()] = request($hiddenKey, '');
        }

        return $values;
    }

    /**
     * @throws Throwable
     */
    public function save(Model $item): Model
    {
        $requestValue = $this->requestValue();
        $oldValues = collect(request("hidden_{$this->field()}", []));

        $saveValue = $this->isMultiple() ? $oldValues : $oldValues->first();

        if ($requestValue !== false) {
            if ($this->isMultiple()) {
                $paths = [];

                foreach ($requestValue as $file) {
                    $paths[] = $this->store($file);
                }

                $saveValue = $saveValue->merge($paths)
                    ->values()
                    ->unique()
                    ->toArray();
            } else {
                $saveValue = $this->store($requestValue);
            }
        }

        $item->{$this->field()} = $saveValue;

        return $item;
    }

    public function formViewValue(Model $item): Collection|string
    {
        if ($this->isMultiple() && ! $item->{$this->field()} instanceof Collection) {
            return collect($item->{$this->field()});
        }

        return $item->{$this->field()} ?? '';
    }

    public function exportViewValue(Model $item): string
    {
        if ($this->isMultiple()) {
            return collect($item->{$this->field()})->implode(';');
        }

        return $item->{$this->field()} ?? '';
    }

    public function acceptExtension(): string
    {
        $extensions = array_map(static function ($val) {
            return '.' . $val;
        }, $this->allowedExtensions);

        return implode(',', $extensions);
    }

    /**
     * @deprecated Will be removed
     * @param  string  $withPrefix
     * @return $this
     */
    public function withPrefix(string $withPrefix): static
    {
        $this->withPrefix = $withPrefix;

        return $this;
    }

    /**
     * @deprecated Will be removed
     * @return string
     */
    public function prefix(): string
    {
        return $this->withPrefix;
    }

    /**
     * @deprecated Will be removed
     * @param  string|bool|null  $value
     * @return string
     */
    protected function unPrefixedValue(string|bool|null $value): string
    {
        return $value ? ltrim($value, $this->prefix()) : '';
    }

    /**
     * @deprecated Will be removed
     * @param  string|bool|null  $value
     * @return string
     */
    protected function prefixedValue(string|bool|null $value): string
    {
        return $value ? ($this->prefix() . ltrim($value, $this->prefix())) : '';
    }
}

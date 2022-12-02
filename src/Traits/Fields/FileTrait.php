<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Leeto\MoonShine\Exceptions\FieldException;
use Leeto\MoonShine\Helpers\Condition;
use Throwable;

trait FileTrait
{
    protected string $disk = 'public';

    protected string $dir = '/';

    protected string $withPrefix = '';

    protected array $allowedExtensions = [];

    protected bool $disableDownload = false;

    public function withPrefix(string $withPrefix): static
    {
        $this->withPrefix = $withPrefix;

        return $this;
    }

    public function disk(string $disk): static
    {
        $this->disk = $disk;

        return $this;
    }

    public function getDisk(): string
    {
        return $this->disk;
    }

    public function dir(string $dir): static
    {
        $this->dir = $dir;

        return $this;
    }

    public function getDir(): string
    {
        return $this->dir;
    }

    public function allowedExtensions(array $allowedExtensions): static
    {
        $this->allowedExtensions = $allowedExtensions;

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
            || in_array($extension, $this->getAllowedExtensions());
    }

    public function disableDownload($condition = null): static
    {
        $this->disableDownload = Condition::boolean($condition, true);

        return $this;
    }

    public function canDownload(): bool
    {
        return !$this->disableDownload;
    }

    /**
     * @throws Throwable
     */
    public function store(UploadedFile $file): string
    {
        $extension = $file->extension();

        throw_if(
            !$this->isAllowedExtension($extension),
            new FieldException("$extension not allowed")
        );

        return $file->store($this->getDir(), $this->getDisk());
    }

    /**
     * @throws Throwable
     */
    public function save(Model $item): Model
    {
        $requestValue = $this->requestValue();
        $oldValues = collect(request("hidden_{$this->field()}", []));
        $saveValue = $this->isMultiple() ? $oldValues : null;

        if ($requestValue !== false) {
            if ($this->isMultiple()) {
                $paths = [];

                foreach ($requestValue as $file) {
                    $paths[] = $this->prefixedValue($this->store($file));
                }

                $saveValue = $saveValue->merge($paths)->unique()->toArray();
            } else {
                $saveValue = $this->prefixedValue($this->store($requestValue));
            }
        }

        if ($saveValue) {
            $item->{$this->field()} = $saveValue;
        }

        return $item;
    }

    public function formViewValue(Model $item): mixed
    {
        if ($this->isMultiple()) {
            return collect($item->{$this->field()})
                ->map(fn($value) => $this->prefixedValue($value));
        }

        return $this->prefixedValue($item->{$this->field()});
    }

    protected function prefixedValue(string $value): string
    {
        return ltrim($value, $this->withPrefix);
    }

    public function exportViewValue(Model $item): string
    {
        if ($this->isMultiple()) {
            return collect($item->{$this->field()})->implode(';');
        }

        return $item->{$this->field()} ?? '';
    }
}

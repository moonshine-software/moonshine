<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Leeto\MoonShine\Exceptions\FieldException;
use Leeto\MoonShine\Helpers\Condition;
use Leeto\MoonShine\Traits\WithStorage;
use Throwable;

trait FileTrait
{
    use WithStorage;

    protected string $withPrefix = '';

    protected array $allowedExtensions = [];

    protected bool $disableDownload = false;

    public function withPrefix(string $withPrefix): static
    {
        $this->withPrefix = $withPrefix;

        return $this;
    }

    public function prefix(): string
    {
        return $this->withPrefix;
    }

    public function allowedExtensions(array $allowedExtensions): static
    {
        $this->allowedExtensions = $allowedExtensions;
        if (!empty($allowedExtensions))
            $this->setAttribute("accept",$this->acceptExtension());
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
        return ! $this->disableDownload;
    }

    public function path(string $value): string
    {
        return Storage::disk($this->getDisk())
            ->url($this->unPrefixedValue($value));
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

        return $this->prefixedValue(
            $file->store($this->getDir(), $this->getDisk())
        );
    }

    /**
     * @throws Throwable
     */
    public function hasManyOrOneSave($hiddenKey, array $values = []): array
    {
        if ($this->isMultiple()) {
            $saveValues = collect(request($hiddenKey, []))
                ->map(fn ($file) => $this->prefixedValue($file));

            if (isset($values[$this->field()])) {
                $saveValues = $saveValues->merge([
                    $this->store($values[$this->field()]),
                ]);
            }

            $values[$this->field()] = $saveValues->values()
                ->unique()
                ->map(fn ($file) => $this->prefixedValue($file))
                ->toArray();
        } elseif (isset($values[$this->field()])) {
            $values[$this->field()] = $this->store($values[$this->field()]);
        }

        return $values;
    }

    /**
     * @throws Throwable
     */
    public function save(Model $item): Model
    {
        $requestValue = $this->requestValue();
        $oldValues = collect(request("hidden_{$this->field()}", []))
            ->map(fn ($file) => $this->prefixedValue($file));

        $saveValue = $this->isMultiple() ? $oldValues : null;

        if ($requestValue !== false) {
            if ($this->isMultiple()) {
                $paths = [];

                foreach ($requestValue as $file) {
                    $paths[] = $this->store($file);
                }

                $saveValue = $saveValue->merge($paths)
                    ->values()
                    ->map(fn ($file) => $this->prefixedValue($file))
                    ->unique()
                    ->toArray();
            } else {
                $saveValue = $this->store($requestValue);
            }
        }

        if ($saveValue) {
            $item->{$this->field()} = $saveValue;
        }

        return $item;
    }

    public function formViewValue(Model $item): Collection|string
    {
        if ($this->isMultiple()) {
            return collect($item->{$this->field()})
                ->map(fn ($value) => $this->unPrefixedValue($value));
        }

        return $this->unPrefixedValue($item->{$this->field()});
    }

    protected function unPrefixedValue(?string $value): string
    {
        return $value ? ltrim($value, $this->prefix()) : '';
    }

    protected function prefixedValue(?string $value): string
    {
        return $value ? ($this->prefix() . ltrim($value, $this->prefix())) : '';
    }

    public function exportViewValue(Model $item): string
    {
        if ($this->isMultiple()) {
            return collect($item->{$this->field()})->implode(';');
        }

        return $item->{$this->field()} ?? '';
    }

    public function acceptExtension() : string
    {
        $extensions = array_map(function ($val) {
            return "." . $val;
        }, $this->allowedExtensions);
        return implode(",", $extensions);
    }
}

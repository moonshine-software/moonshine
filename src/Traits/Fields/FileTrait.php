<?php

namespace Leeto\MoonShine\Traits\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Leeto\MoonShine\Exceptions\FieldException;
use Throwable;

trait FileTrait
{
    protected string $disc = 'public';

    protected string $dir = '/';

    protected array $allowedExtension = [];

    protected bool $disableDownload = false;

    public function disc(string $disc): static
    {
        $this->disc = $disc;

        return $this;
    }

    public function getDisc(): string
    {
        return $this->disc;
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

    public function allowedExtension(array $allowedExtension): static
    {
        $this->allowedExtension = $allowedExtension;

        return $this;
    }

    public function getAllowedExtension(): array
    {
        return $this->allowedExtension;
    }

    public function isAllowedExtension(string $extension): bool
    {
        return in_array($extension, $this->getAllowedExtension());
    }

    public function disableDownload(): static
    {
        $this->disableDownload = true;

        return $this;
    }

    public function canDownload(): bool
    {
        return !$this->disableDownload;
    }

    /**
     * @throws Throwable
     */
    private function store(UploadedFile $file): string
    {
        $extension = $file->extension();

        throw_if(
            !$this->isAllowedExtension($extension),
            new FieldException("$extension not allowed")
        );

        return $file->store($this->getDir(), $this->getDisc());
    }

    public function save(Model $item): Model
    {
        $requestValue = $this->requestValue();
        $oldValues = collect(request("hidden_{$this->field()}", []));
        $saveValue = $this->isMultiple() ? $oldValues : null;

        if ($requestValue !== false) {
            if($this->isMultiple()) {
                $paths = [];

                foreach ($requestValue as $file) {
                    $paths[] = $this->store($file);
                }

                $saveValue = $saveValue->merge($paths)->unique()->toArray();
            } else {
                $saveValue = $this->store($requestValue);
            }
        }

        if($saveValue) {
            $item->{$this->field()} = $saveValue;
        }

        return $item;
    }

    public function exportViewValue(Model $item): string
    {
        if($this->isMultiple()) {
            return collect($item->{$this->field()})->implode(';');
        }

        return parent::exportViewValue($item);
    }
}
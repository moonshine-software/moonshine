<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Fields;

use Illuminate\Http\UploadedFile;
use Leeto\MoonShine\Exceptions\FieldException;
use Leeto\MoonShine\Helpers\Condition;
use Throwable;

trait FileTrait
{
    protected string $disk = 'public';

    protected string $dir = '/';

    /**
     * @var array<string>
     */
    protected array $allowedExtensions = [];

    protected bool $disableDownload = false;

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

    /**
     * @param  array<string>  $allowedExtensions
     * @return $this
     */
    public function allowedExtensions(array $allowedExtensions): static
    {
        $this->allowedExtensions = $allowedExtensions;

        return $this;
    }

    /**
     * @return array<string>
     */
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
    private function store(UploadedFile $file): string
    {
        $extension = $file->extension();

        throw_if(
            !$this->isAllowedExtension($extension),
            new FieldException("$extension not allowed")
        );

        return $file->store($this->getDir(), $this->getDisk());
    }
}

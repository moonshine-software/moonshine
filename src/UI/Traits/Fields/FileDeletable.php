<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

trait FileDeletable
{
    protected bool $isDeleteFiles = true;

    protected bool $isDeleteDir = false;

    public function disableDeleteFiles(): static
    {
        $this->isDeleteFiles = false;

        return $this;
    }

    public function enableDeleteDir(): static
    {
        $this->isDeleteDir = true;

        return $this;
    }

    public function deleteFile(string $fileName): bool
    {
        if (! $this->isDeleteFiles()) {
            return false;
        }

        return $this->deleteStorageFile(
            $this->getPrependedDir($fileName)
        );
    }

    public function isDeleteFiles(): bool
    {
        return $this->isDeleteFiles;
    }

    protected function deleteDir(): void
    {
        if (
            $this->isDeleteDir()
            && empty(
                $this->getStorageDirectories(
                    $this->getDir()
                )
            )
            && empty($this->getStorageFiles($this->getDir()))
        ) {
            $this->deleteStorageDirectory($this->getDir());
        }
    }

    public function isDeleteDir(): bool
    {
        return $this->isDeleteDir;
    }
}

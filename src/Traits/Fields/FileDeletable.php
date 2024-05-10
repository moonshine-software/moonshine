<?php

namespace MoonShine\Traits\Fields;

use Illuminate\Support\Facades\Storage;

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

    /**
     * @deprecated unused and will be removed in 3.0
     */
    public function checkAndDelete(
        ?string $storedValue = null,
        ?string $inputValue = null,
    ): void {
        if (! $storedValue) {
            return;
        }

        if ($storedValue !== $inputValue) {
            $this->deleteFile($storedValue);
        }
    }

    public function deleteFile(string $fileName): bool
    {
        if (! $this->isDeleteFiles()) {
            return false;
        }

        return Storage::disk($this->getDisk())->delete(
            $this->prependDir($fileName)
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
                Storage::disk($this->getDisk())->directories(
                    $this->getDir()
                )
            )
            && empty(Storage::disk($this->getDisk())->files($this->getDir()))
        ) {
            Storage::disk($this->getDisk())->deleteDirectory($this->getDir());
        }
    }

    public function isDeleteDir(): bool
    {
        return $this->isDeleteDir;
    }
}

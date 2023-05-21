<?php

namespace MoonShine\Traits\Fields;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

trait FileDeletable
{
    protected bool $isDeleteFiles = true;

    protected bool $isDeleteDir = false;

    public function isDeleteFiles(): bool
    {
        return $this->isDeleteFiles;
    }

    public function isDeleteDir(): bool
    {
        return $this->isDeleteDir;
    }

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

    public function checkAndDelete(
        iterable|string|null $storedValues,
        array $inputValues
    ): void {
        if($storedValues instanceof Collection) {
            $storedValues = $storedValues->toArray();
        }

        if(empty($storedValues)) {
            return;
        }

        if ($this->isMultiple()) {
            foreach ($storedValues as $storedValue) {
                if(! in_array($storedValue, $inputValues)) {
                    $this->deleteFile($storedValue);
                }
            }
        } elseif (! in_array($storedValues, $inputValues)) {
            $this->deleteFile($storedValues);
        }
    }

    public function deleteFile(string $fileName): bool
    {
        if(! $this->isDeleteFiles()) {
            return false;
        }

        return Storage::disk($this->getDisk())->delete($this->prependDir($fileName));
    }

    protected function deleteDir(): void
    {
        if(
            $this->isDeleteDir()
            && empty(Storage::disk($this->getDisk())->directories($this->getDir()))
            && empty(Storage::disk($this->getDisk())->files($this->getDir()))
        ) {
            Storage::disk($this->getDisk())->deleteDirectory($this->getDir());
        }
    }
}

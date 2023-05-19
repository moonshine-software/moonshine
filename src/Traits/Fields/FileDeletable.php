<?php

namespace MoonShine\Traits\Fields;

use Illuminate\Support\Facades\Storage;
use MoonShine\Helpers\Condition;

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

    public function disableDeleteFiles($condition = null): static
    {
        $this->isDeleteFiles = Condition::boolean($condition, false);

        return $this;
    }

    public function enableDeleteDir($condition = null): static
    {
        $this->isDeleteDir = Condition::boolean($condition, true);

        return $this;
    }

    public function checkAndDelete(
        array|string|null $storedValues,
        array|string $inputValues
    ): void {
        if(empty($storedValues)) {
            return;
        }

        if ($this->isMultiple()) {
            foreach ($storedValues as $storedValue) {
                if(!in_array($storedValue, $inputValues)) {
                    $this->deleteFile($storedValue);
                }
            }
        } elseif ($storedValues != $inputValues) {
            $this->deleteFile($storedValues);
        }
    }

    public function deleteFile(string $fileName): bool
    {
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
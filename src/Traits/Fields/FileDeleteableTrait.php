<?php

namespace MoonShine\Traits\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use MoonShine\Fields\HasMany;
use MoonShine\Fields\HasOne;
use MoonShine\Helpers\Condition;

trait FileDeleteableTrait
{
    protected bool $isDeleteFiles = true;

    protected bool $isDeleteDir = false;

    public function isDeleteFiles(): bool
    {
        return $this->isDeleteFiles;
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

    public function deleteFilesFromRelation (
        HasMany $hasManyField,
        Model $item,
        array $ids,
        $primaryKey
    ): void {
        foreach ($item->{$hasManyField->relation()} as $value) {
            if(in_array($value->{$primaryKey}, $ids)) {
                $this->deleteFile($value->{$this->field()});
            }
        }
    }

    public function checkForDeletionFromRelation (
        HasOne|HasMany $checkField,
        Model $item,
        int $index,
        array|string $inputValues
    ): void {
        $storedValues =  $checkField instanceof HasOne
            ? $item->{$checkField->relation()}?->{$this->field()}
            : $item->{$checkField->relation()}[$index]?->{$this->field()};

        $this->checkForDeletion(
            $storedValues,
            $inputValues
        );
    }

    protected function checkForDeletion(
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

    protected function deleteFile(string $fileName): void
    {
        Storage::disk($this->getDisk())->delete($this->prependDir($fileName));
    }

    protected function deleteDir(): void
    {
        if(
            $this->isDeleteDir
            && empty(Storage::disk($this->getDisk())->directories($this->getDir()))
            && empty(Storage::disk($this->getDisk())->files($this->getDir()))
        ) {
            Storage::disk($this->getDisk())->deleteDirectory($this->getDir());
        }
    }
}
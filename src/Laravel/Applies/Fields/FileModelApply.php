<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Applies\Fields;

use Closure;
use Illuminate\Http\UploadedFile;
use MoonShine\Contracts\UI\ApplyContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\UI\Exceptions\FieldException;
use MoonShine\UI\Fields\File;
use Throwable;

final class FileModelApply implements ApplyContract
{
    /* @param  File  $field */
    public function apply(FieldContract $field): Closure
    {
        return function (mixed $item) use ($field): mixed {
            $requestValue = $field->getRequestValue();
            $remainingValues = $field->getRemainingValues();

            data_forget($item, $field->getHiddenRemainingValuesKey());

            $newValue = $field->isMultiple() ? $remainingValues : $remainingValues->first();

            if ($requestValue !== false) {
                if ($field->isMultiple()) {
                    $paths = [];

                    foreach ($requestValue as $file) {
                        $paths[] = $this->store($field, $file);
                    }

                    $newValue = $newValue->merge($paths)
                        ->values()
                        ->unique()
                        ->toArray();
                } else {
                    $newValue = $this->store($field, $requestValue);
                }
            }

            $field->removeExcludedFiles();

            return data_set($item, $field->getColumn(), $newValue);
        };
    }

    /**
     * @throws Throwable
     */
    public function store(File $field, UploadedFile $file): string
    {
        $extension = $file->extension();

        throw_if(
            ! $field->isAllowedExtension($extension),
            new FieldException("$extension not allowed")
        );

        if ($field->isKeepOriginalFileName()) {
            return $file->storeAs(
                $field->getDir(),
                $file->getClientOriginalName(),
                $field->getOptions()
            );
        }

        if (! is_null($field->getCustomName())) {
            return $file->storeAs(
                $field->getDir(),
                value($field->getCustomName(), $file, $this),
                $field->getOptions()
            );
        }

        if(! $result = $file->store($field->getDir(), $field->getOptions())) {
            throw new FieldException('Failed to save file, check your permissions');
        }

        return $result;
    }
}

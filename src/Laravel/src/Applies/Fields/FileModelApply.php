<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Applies\Fields;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use MoonShine\Contracts\UI\ApplyContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Crud\Exceptions\FileFieldException;
use MoonShine\UI\Fields\File;

/**
 * @implements ApplyContract<File>
 */
final class FileModelApply implements ApplyContract
{
    /**
     * @param  File  $field
     */
    public function apply(FieldContract $field): Closure
    {
        return function (mixed $item) use ($field): mixed {
            /** @var Model $item */

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
                    $field->setRemainingValues([]);
                }
            }

            $field->removeExcludedFiles(
                $field->getCustomName() !== null || $field->isKeepOriginalFileName()
                    ? $newValue
                    : null,
            );

            return data_set($item, $field->getColumn(), $newValue);
        };
    }

    public function store(File $field, UploadedFile $file): string
    {
        $extension = $file->extension();

        if (! $field->isAllowedExtension($extension)) {
            throw FileFieldException::extensionNotAllowed($extension);
        }

        if ($field->isKeepOriginalFileName()) {
            return $file->storeAs(
                $field->getDir(),
                $file->getClientOriginalName(),
                $field->getOptions()
            );
        }

        if (! \is_null($field->getCustomName())) {
            return $file->storeAs(
                $field->getDir(),
                \call_user_func($field->getCustomName(), $file, $field),
                $field->getOptions()
            );
        }

        if (! $result = $file->store($field->getDir(), $field->getOptions())) {
            throw FileFieldException::failedSave();
        }

        return $result;
    }
}

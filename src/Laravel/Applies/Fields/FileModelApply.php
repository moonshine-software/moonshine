<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Applies\Fields;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use MoonShine\Contracts\ApplyContract;
use MoonShine\UI\Exceptions\FieldException;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\File;
use Throwable;

final class FileModelApply implements ApplyContract
{
    /* @param  File  $field */
    public function apply(Field $field): Closure
    {
        return function (Model $item) use ($field): Model {
            $requestValue = $field->getRequestValue();

            $oldValues = request()->collect($field->hiddenOldValuesKey());
            $values = collect(data_get($item, $field->getColumn(), []));

            data_forget($item, $field->hiddenOldValuesKey());

            $saveValue = $field->isMultiple() ? $oldValues : $oldValues->first();

            if ($requestValue !== false) {
                if ($field->isMultiple()) {
                    $paths = [];

                    foreach ($requestValue as $file) {
                        $paths[] = $this->store($field, $file);
                    }

                    $saveValue = $saveValue->merge($paths)
                        ->values()
                        ->unique()
                        ->toArray();
                } else {
                    $saveValue = $this->store($field, $requestValue);
                }
            }

            $removedValues = $values->diff(
                $saveValue
            );

            $removedValues->each(fn (string $file) => $field->deleteFile($file));

            return data_set($item, $field->getColumn(), $saveValue);
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
                $field->parseOptions()
            );
        }

        if (! is_null($field->getCustomName())) {
            return $file->storeAs(
                $field->getDir(),
                value($field->getCustomName(), $file, $this),
                $field->parseOptions()
            );
        }

        if(! $result = $file->store($field->getDir(), $field->parseOptions())) {
            throw new FieldException('Failed to save file, check your permissions');
        }

        return $result;
    }
}

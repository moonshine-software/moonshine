<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Stringable;
use MoonShine\Exceptions\FieldException;
use MoonShine\Helpers\Condition;
use MoonShine\Traits\WithStorage;
use Throwable;

trait FileTrait
{
    use WithStorage;

    protected array $allowedExtensions = [];

    protected bool $disableDownload = false;

    protected bool $keepOriginalFileName = false;

    protected ?Closure $customName = null;

    public function keepOriginalFileName(): static
    {
        $this->keepOriginalFileName = true;

        return $this;
    }

    public function customName(Closure $name): static
    {
        $this->customName = $name;

        return $this;
    }

    public function allowedExtensions(array $allowedExtensions): static
    {
        $this->allowedExtensions = $allowedExtensions;
        if ($allowedExtensions !== []) {
            $this->setAttribute("accept", $this->acceptExtension());
        }

        return $this;
    }

    public function acceptExtension(): string
    {
        $extensions = array_map(
            static fn ($val): string => '.' . $val,
            $this->allowedExtensions
        );

        return implode(',', $extensions);
    }

    public function disableDownload($condition = null): static
    {
        $this->disableDownload = Condition::boolean($condition, true);

        return $this;
    }

    public function canDownload(): bool
    {
        return ! $this->disableDownload;
    }

    public function pathWithDir(string $value): string
    {
        return $this->path($this->prependDir($value));
    }

    public function path(string $value): string
    {
        return Storage::disk($this->getDisk())->url($value);
    }

    public function prependDir(string $value): string
    {
        $dir = empty($this->getDir()) ? '' : $this->getDir() . '/';

        return str($value)->remove($dir)
            ->prepend($dir)
            ->value();
    }

    /**
     * @throws Throwable
     */
    public function hasManyOrOneSave(
        array|UploadedFile|null $valueOrValues = null
    ): array|string|null {
        if ($this->isMultiple()) {
            throw_if(
                ! is_null($valueOrValues) && ! is_array($valueOrValues),
                new FieldException('Files must be an array')
            );

            $saveValues = request()
                ->collect($this->hiddenOldValuesKey())
                ->reject(fn ($v): bool => is_numeric($v));

            if ($valueOrValues) {
                foreach ($valueOrValues as $value) {
                    $saveValues = $saveValues->merge([
                        $this->store($value),
                    ]);
                }
            }


            $valueOrValues = $saveValues->values()
                ->filter()
                ->unique()
                ->toArray();
        } elseif ($valueOrValues instanceof UploadedFile) {
            $valueOrValues = $this->store($valueOrValues);
        } elseif (empty($valueOrValues)) {
            $valueOrValues = request(
                $this->hiddenOldValuesKey(),
                $this->isNullable() ? null : ''
            );
        }

        return $valueOrValues;
    }

    public function hiddenOldValuesKey(): string
    {
        return str('hidden_')
            ->when(
                $this->parentRequestValueKey(),
                fn (Stringable $str): Stringable => $str->append(
                    $this->parentRequestValueKey() . "."
                )
            )
            ->append($this->field())
            ->value();
    }

    /**
     * @throws Throwable
     */
    public function store(UploadedFile $file): string
    {
        $extension = $file->extension();

        throw_if(
            ! $this->isAllowedExtension($extension),
            new FieldException("$extension not allowed")
        );

        if ($this->keepOriginalFileName) {
            return $file->storeAs(
                $this->getDir(),
                $file->getClientOriginalName(),
                $this->getDisk()
            );
        }

        if (is_callable($this->customName)) {
            return $file->storeAs(
                $this->getDir(),
                call_user_func($this->customName, $file),
                $this->getDisk()
            );
        }

        return $file->store($this->getDir(), $this->getDisk());
    }

    public function isAllowedExtension(string $extension): bool
    {
        return empty($this->getAllowedExtensions())
            || in_array($extension, $this->getAllowedExtensions(), true);
    }

    public function getAllowedExtensions(): array
    {
        return $this->allowedExtensions;
    }

    /**
     * @throws Throwable
     */
    public function save(Model $item): Model
    {
        $requestValue = $this->requestValue();
        $oldValues = request()
            ->collect($this->hiddenOldValuesKey());

        if ($this->isDeleteFiles()) {
            $this->checkAndDelete(
                $item->{$this->field()},
                $oldValues->toArray()
            );
        }

        $saveValue = $this->isMultiple() ? $oldValues : $oldValues->first();

        if ($requestValue !== false) {
            if ($this->isMultiple()) {
                $paths = [];

                foreach ($requestValue as $file) {
                    $paths[] = $this->store($file);
                }

                $saveValue = $saveValue->merge($paths)
                    ->values()
                    ->unique()
                    ->toArray();
            } else {
                $saveValue = $this->store($requestValue);
            }
        }

        $item->{$this->field()} = $saveValue;

        return $item;
    }

    public function formViewValue(Model $item): Collection|string
    {
        if ($this->isMultiple() && ! $item->{$this->field()} instanceof Collection) {
            return collect($item->{$this->field()});
        }

        return $item->{$this->field()} ?? '';
    }

    public function exportViewValue(Model $item): string
    {
        if ($this->isMultiple()) {
            return collect($item->{$this->field()})->implode(';');
        }

        return $item->{$this->field()} ?? '';
    }
}

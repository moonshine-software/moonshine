<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Actions;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Leeto\MoonShine\Contracts\Actions\ActionContract;
use Leeto\MoonShine\Contracts\Resources\ResourceContract;
use Leeto\MoonShine\Exceptions\ActionException;
use Leeto\MoonShine\Fields\Field;
use Leeto\MoonShine\Jobs\ImportActionJob;
use Leeto\MoonShine\Traits\WithQueue;
use Leeto\MoonShine\Traits\WithStorage;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Exception\UnsupportedTypeException;
use OpenSpout\Reader\Exception\ReaderNotOpenedException;
use Rap2hpoutre\FastExcel\FastExcel;

class ImportAction extends Action implements ActionContract
{
    use WithStorage;
    use WithQueue;

    protected static string $view = 'moonshine::actions.import';

    public string $triggerKey = 'importAction';

    public string $inputName = 'import_file';

    protected bool $deleteAfter = false;

    /**
     * @throws IOException
     * @throws ActionException
     * @throws ReaderNotOpenedException
     * @throws UnsupportedTypeException
     */
    public function handle(): RedirectResponse
    {
        if (! request()->hasFile($this->inputName)) {
            return redirect()
                ->back()
                ->with('alert', trans('moonshine::ui.resource.import.file_required'));
        }

        if (! in_array(request()->file($this->inputName)->extension(), ['csv', 'xlsx'])) {
            return redirect()
                ->back()
                ->with('alert', trans('moonshine::ui.resource.import.extension_not_supported'));
        }

        if (is_null($this->resource())) {
            throw new ActionException('Resource is required for action');
        }

        $this->resolveStorage();

        $path = request()->file($this->inputName)->store(
            $this->getDir(),
            $this->getDisk()
        );

        $path = Storage::disk($this->getDisk())
            ->path($path);

        if ($this->isQueue()) {
            ImportActionJob::dispatch(get_class($this->resource()), $path, $this->deleteAfter);

            return redirect()
                ->back()
                ->with('alert', trans('moonshine::ui.resource.queued'));
        }

        self::process($path, $this->resource(), $this->deleteAfter);

        return redirect()
            ->back()
            ->with('alert', trans('moonshine::ui.resource.import.imported'));
    }

    /**
     * @throws IOException
     * @throws UnsupportedTypeException
     * @throws ReaderNotOpenedException
     */
    public static function process(string $path, ResourceContract $resource, bool $deleteAfter = false): Collection
    {
        $result = (new FastExcel())->import($path, function ($line) use ($resource) {
            $data = collect($line)->mapWithKeys(function ($value, $key) use ($resource) {
                $field = $resource->importFields()->first(function ($field) use ($key) {
                    return $field->field() === $key || $field->label() === $key;
                });

                if (! $field instanceof Field) {
                    return [];
                }

                return [$field->field() => $value];
            })->toArray();

            $item = isset($data[$resource->getModel()->getKeyName()])
                ? $resource->getModel()
                    ->newModelQuery()
                    ->find($data[$resource->getModel()->getKeyName()])
                : $resource->getModel()
            ;

            return $resource->save(
                $item,
                fields: $resource->importFields(),
                saveData: $data
            );
        });

        if ($deleteAfter) {
            unlink($path);
        }

        return $result;
    }

    public function isTriggered(): bool
    {
        return request()->has($this->triggerKey);
    }

    /**
     * @throws ActionException
     */
    public function url(): string
    {
        if (is_null($this->resource())) {
            throw new ActionException('Resource is required for action');
        }

        return $this->resource()->route('actions');
    }

    public function deleteAfter(): self
    {
        $this->deleteAfter = true;

        return $this;
    }
}

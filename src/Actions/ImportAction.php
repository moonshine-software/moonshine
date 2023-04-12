<?php

declare(strict_types=1);

namespace MoonShine\Actions;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Exceptions\ActionException;
use MoonShine\Fields\Field;
use MoonShine\Jobs\ImportActionJob;
use MoonShine\Notifications\MoonShineNotification;
use MoonShine\Traits\WithQueue;
use MoonShine\Traits\WithStorage;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Exception\UnsupportedTypeException;
use OpenSpout\Reader\Exception\ReaderNotOpenedException;
use Rap2hpoutre\FastExcel\FastExcel;

class ImportAction extends Action
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
            return back()
                ->with('alert', trans('moonshine::ui.resource.import.file_required'));
        }

        if (! in_array(request()->file($this->inputName)->extension(), ['csv', 'xlsx'])) {
            return back()
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
            ImportActionJob::dispatch(
                get_class($this->resource()),
                $path,
                $this->deleteAfter
            );

            return back()
                ->with('alert', trans('moonshine::ui.resource.queued'));
        }

        self::process($path, $this->resource(), $this->deleteAfter);

        return back()
            ->with('alert', trans('moonshine::ui.resource.import.imported'));
    }

    /**
     * @throws IOException
     * @throws UnsupportedTypeException
     * @throws ReaderNotOpenedException
     */
    public static function process(
        string $path,
        ResourceContract $resource,
        bool $deleteAfter = false
    ): Collection {
        $result = (new FastExcel())->import($path, function ($line) use ($resource) {
            $data = collect($line)->mapWithKeys(function ($value, $key) use ($resource) {
                $field = $resource->getFields()->importFields()->first(function ($field) use ($key) {
                    return $field->field() === $key || $field->label() === $key;
                });

                if (! $field instanceof Field) {
                    return [];
                }

                return [$field->field() => $value];
            })->toArray();

            if (($data[$resource->getModel()->getKeyName()] ?? '') === '') {
                unset($data[$resource->getModel()->getKeyName()]);
            }

            $item = isset($data[$resource->getModel()->getKeyName()])
                ? $resource->getModel()
                    ->newModelQuery()
                    ->find($data[$resource->getModel()->getKeyName()])
                : $resource->getModel();

            return $resource->save(
                $item,
                fields: $resource->getFields()->importFields(),
                saveData: $data
            );
        });

        if ($deleteAfter) {
            unlink($path);
        }

        MoonShineNotification::send(
            trans('moonshine::ui.resource.import.imported')
        );

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

        return $this->resource()->route('actions.index');
    }

    public function deleteAfter(): self
    {
        $this->deleteAfter = true;

        return $this;
    }
}

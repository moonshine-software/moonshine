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
use MoonShine\MoonShineUI;
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

    protected ?string $icon = 'heroicons.outline.paper-clip';

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
            MoonShineUI::toast(
                __('moonshine::ui.resource.import.file_required'),
                'error'
            );

            return back();
        }

        if (! in_array(request()->file($this->inputName)->extension(), ['csv', 'xlsx'])) {
            MoonShineUI::toast(
                __('moonshine::ui.resource.import.extension_not_supported'),
                'error'
            );

            return back();
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

            MoonShineUI::toast(
                __('moonshine::ui.resource.queued')
            );

            return back();
        }

        self::process($path, $this->resource(), $this->deleteAfter);

        MoonShineUI::toast(
            __('moonshine::ui.resource.import.imported'),
            'success'
        );

        return back();
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

    public function deleteAfter(): self
    {
        $this->deleteAfter = true;

        return $this;
    }
}

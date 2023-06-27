<?php

declare(strict_types=1);

namespace MoonShine\Actions;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use MoonShine\Contracts\Fields\HasDefaultValue;
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
    public string $inputName = 'import_file';
    protected ?string $icon = 'heroicons.outline.paper-clip';
    protected bool $deleteAfter = false;

    protected string $csvDelimiter = ',';

    public function delimiter(string $value): static
    {
        $this->csvDelimiter = $value;

        return $this;
    }

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

        $requestFile = request()->file($this->inputName);

        if (! in_array(
            $requestFile->getClientOriginalExtension(),
            ['csv', 'xlsx']
        )) {
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

        $path = request()->file($this->inputName)->storeAs(
            $this->getDir(),
            str_replace('.txt', '.csv', (string) $requestFile->hashName()),
            $this->getDisk()
        );

        $path = Storage::disk($this->getDisk())
            ->path($path);

        if ($this->isQueue()) {
            ImportActionJob::dispatch(
                $this->resource()::class,
                $path,
                $this->deleteAfter,
                $this->getDelimiter()
            );

            MoonShineUI::toast(
                __('moonshine::ui.resource.queued')
            );

            return back();
        }

        self::process(
            $path,
            $this->resource(),
            $this->deleteAfter,
            $this->getDelimiter()
        );

        MoonShineUI::toast(
            __('moonshine::ui.resource.import.imported'),
            'success'
        );

        return back();
    }

    public function getDelimiter(): string
    {
        return $this->csvDelimiter;
    }

    /**
     * @throws IOException
     * @throws UnsupportedTypeException
     * @throws ReaderNotOpenedException
     */
    public static function process(
        string $path,
        ResourceContract $resource,
        bool $deleteAfter = false,
        string $delimiter = ','
    ): Collection {
        $fastExcel = new FastExcel();

        if (str($path)->contains('.csv')) {
            $fastExcel->configureCsv($delimiter);
        }

        $result = $fastExcel->import($path, function ($line) use ($resource) {
            $data = collect($line)->mapWithKeys(
                function ($value, $key) use ($resource) {
                    $field = $resource->getFields()->importFields()->first(
                        fn ($field): bool => $field->field() === $key || $field->label() === $key
                    );

                    if (! $field instanceof Field) {
                        return [];
                    }

                    if (empty($value)) {
                        $value = $field instanceof HasDefaultValue
                            ? $field->getDefault()
                            : ($field->isNullable() ? null : $value);
                    }

                    return [$field->field() => $value];
                }
            )->toArray();

            if (($data[$resource->getModel()->getKeyName()] ?? '') === '') {
                unset($data[$resource->getModel()->getKeyName()]);
            }

            if ($data === []) {
                return false;
            }

            $item = isset($data[$resource->getModel()->getKeyName()])
                ? $resource->getModel()
                    ->newModelQuery()
                    ->findOrNew($data[$resource->getModel()->getKeyName()])
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

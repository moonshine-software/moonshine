<?php

declare(strict_types=1);

namespace MoonShine\Handlers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Exceptions\ActionException;
use MoonShine\Fields\Field;
use MoonShine\Jobs\ImportHandlerJob;
use MoonShine\MoonShineUI;
use MoonShine\Notifications\MoonShineNotification;
use MoonShine\Resources\ModelResource;
use MoonShine\Traits\WithStorage;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Exception\UnsupportedTypeException;
use OpenSpout\Reader\Exception\ReaderNotOpenedException;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\Response;

class ImportHandler extends Handler
{
    use WithStorage;

    protected string $view = 'moonshine::actions.import';

    public string $inputName = 'import_file';

    protected ?string $icon = 'heroicons.outline.paper-clip';

    protected bool $deleteAfter = false;

    protected string $csvDelimiter = ',';

    public function getInputName(): string
    {
        return $this->inputName;
    }

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
    public function handle(): Response
    {
        if (! request()->hasFile($this->getInputName())) {
            MoonShineUI::toast(
                __('moonshine::ui.resource.import.file_required'),
                'error'
            );

            return back();
        }

        $requestFile = request()->file($this->getInputName());

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

        if (! $this->hasResource()) {
            throw ActionException::resourceRequired();
        }

        $this->resolveStorage();

        $path = request()->file($this->getInputName())->storeAs(
            $this->getDir(),
            str_replace('.txt', '.csv', (string) $requestFile->hashName()),
            $this->getDisk()
        );

        $path = Storage::disk($this->getDisk())
            ->path($path);

        if ($this->isQueue()) {
            ImportHandlerJob::dispatch(
                $this->getResource()::class,
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
            $this->getResource(),
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
     * @param ModelResource $resource
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
                function ($value, $key) use ($resource): array {
                    $field = $resource->getImportFields()->first(
                        fn (Field $field): bool => $field->column() === $key || $field->label() === $key
                    );

                    if (! $field instanceof Field) {
                        return [];
                    }

                    if (empty($value) && $field instanceof HasDefaultValue) {
                        $value = $field->getDefault();
                    }

                    if(empty($value) && $field->isNullable()) {
                        $value = null;
                    }

                    $value = is_string($value) && str($value)->isJson()
                        ? json_decode($value, null, 512, JSON_THROW_ON_ERROR)
                        : $value;

                    return [$field->column() => $field->getValueFromRaw($value)];
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

            if (is_null($item)) {
                return false;
            }

            $data = $resource->beforeImportFilling($data);

            $item->forceFill($data);

            $item = $resource->beforeImported($item);

            return tap($item->save(), fn () => $resource->afterImported($item));
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

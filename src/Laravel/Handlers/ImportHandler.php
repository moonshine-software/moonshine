<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Handlers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use MoonShine\Core\Contracts\ResourceContract;
use MoonShine\Core\Handlers\Handler;
use MoonShine\Laravel\Jobs\ImportHandlerJob;
use MoonShine\Laravel\MoonShineUI;
use MoonShine\Laravel\Notifications\MoonShineNotification;
use MoonShine\Support\Enums\ToastType;
use MoonShine\Support\Traits\WithStorage;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Contracts\Fields\HasDefaultValue;
use MoonShine\UI\Exceptions\ActionButtonException;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\File;
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

    protected ?string $icon = 'paper-clip';

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

    public function getDelimiter(): string
    {
        return $this->csvDelimiter;
    }

    public function deleteAfter(): self
    {
        $this->deleteAfter = true;

        return $this;
    }

    /**
     * @throws IOException
     * @throws ActionButtonException
     * @throws ReaderNotOpenedException
     * @throws UnsupportedTypeException
     */
    public function handle(): Response
    {
        if (! request()->hasFile($this->getInputName())) {
            MoonShineUI::toast(
                __('moonshine::ui.resource.import.file_required'),
                ToastType::ERROR
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
                ToastType::ERROR
            );

            return back();
        }

        if (! $this->hasResource()) {
            throw ActionButtonException::resourceRequired();
        }

        $this->resolveStorage();

        $path = request()->file($this->getInputName())?->storeAs(
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
            ToastType::SUCCESS
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
        bool $deleteAfter = false,
        string $delimiter = ','
    ): Collection {
        $fastExcel = new FastExcel();

        if (str($path)->contains('.csv')) {
            $fastExcel->configureCsv($delimiter);
        }

        $result = $fastExcel->import($path, function ($line) use ($resource) {
            $data = collect($line)->mapWithKeys(
                function (mixed $value, string $key) use ($resource): array {
                    $field = $resource->getImportFields()->first(
                        fn (Field $field): bool => $field->getColumn() === $key || $field->getLabel() === $key
                    );

                    if (! $field instanceof Field) {
                        return [];
                    }

                    if (empty($value) && $field instanceof HasDefaultValue) {
                        $value = $field->getDefault();
                    }

                    if (empty($value) && $field->isNullable()) {
                        $value = null;
                    }

                    $value = is_string($value) && str($value)->isJson()
                        ? json_decode($value, null, 512, JSON_THROW_ON_ERROR)
                        : $value;

                    return [$field->getColumn() => $value];
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

            $item->forceFill($data);

            return $item->save();
        });

        if ($deleteAfter) {
            unlink($path);
        }

        MoonShineNotification::send(
            trans('moonshine::ui.resource.import.imported')
        );

        return $result;
    }

    /**
     * @throws ActionButtonException
     */
    public function getButton(): ActionButton
    {
        if (! $this->hasResource()) {
            throw ActionButtonException::resourceRequired();
        }

        return ActionButton::make(
            $this->getLabel(),
            '#'
        )
            ->success()
            ->icon($this->getIconValue(), $this->isCustomIcon(), $this->getIconPath())
            ->inOffCanvas(
                fn (): string => $this->getLabel(),
                fn (): FormBuilder => FormBuilder::make(
                    $this->getResource()?->getRoute('handler', query: ['handlerUri' => $this->getUriKey()]) ?? ''
                )
                    ->fields([
                        File::make(column: $this->getInputName())->required(),
                    ])
                    ->submit(__('moonshine::ui.confirm')),
                name: 'import-off-canvas'
            );
    }
}

<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Handlers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Laravel\Jobs\ImportHandlerJob;
use MoonShine\Laravel\MoonShineUI;
use MoonShine\Laravel\Notifications\MoonShineNotification;
use MoonShine\Support\Enums\ToastType;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Contracts\HasDefaultValueContract;
use MoonShine\UI\Exceptions\ActionButtonException;
use MoonShine\UI\Fields\File;
use MoonShine\UI\Traits\WithStorage;
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

    public function deleteAfter(): static
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
                $this->getDelimiter(),
                $this->getNotifyUsers(),
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
            $this->getDelimiter(),
            $this->getNotifyUsers(),
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
        string $delimiter = ',',
        array $notifyUsers = [],
    ): Collection {
        $fastExcel = new FastExcel();

        if (str($path)->contains('.csv')) {
            $fastExcel->configureCsv($delimiter);
        }

        $result = $fastExcel->import($path, static function ($line) use ($resource) {
            $data = collect($line)->mapWithKeys(
                static function (mixed $value, string $key) use ($resource): array {
                    $field = $resource->getImportFields()->first(
                        static fn (FieldContract $field
                        ): bool => $field->getColumn() === $key || $field->getLabel() === $key
                    );

                    if (! $field instanceof FieldContract) {
                        return [];
                    }

                    if (empty($value) && $field instanceof HasDefaultValueContract) {
                        $value = $field->getDefault();
                    }

                    if (empty($value) && $field->isNullable()) {
                        $value = null;
                    }

                    $value = is_string($value) && str($value)->isJson()
                        ? json_decode($value, null, 512, JSON_THROW_ON_ERROR)
                        : $value;

                    return [$field->getColumn() => $field->getValueFromRaw($value)];
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

            $data = $resource->beforeImportFilling($data);

            $item->forceFill($data);

            $item = $resource->beforeImported($item);

            return tap($item->save(), fn () => $resource->afterImported($item));
        });

        if ($deleteAfter) {
            unlink($path);
        }

        MoonShineNotification::send(
            __('moonshine::ui.resource.import.imported'),
            ids: $notifyUsers
        );

        return $result;
    }

    /**
     * @throws ActionButtonException
     */
    public function getButton(): ActionButtonContract
    {
        if (! $this->hasResource()) {
            throw ActionButtonException::resourceRequired();
        }

        return $this->prepareButton(
            ActionButton::make(
                $this->getLabel(),
                '#'
            )
                ->success()
                ->icon($this->getIconValue(), $this->isCustomIcon(), $this->getIconPath())
                ->inOffCanvas(
                    fn (): string => $this->getLabel(),
                    fn (): FormBuilderContract => FormBuilder::make($this->getUrl())->fields([
                        File::make(column: $this->getInputName())->required(),
                    ])
                        ->class('js-change-query')
                        ->customAttributes([
                            'data-original-url' => $this->getUrl(),
                        ])
                        ->submit(__('moonshine::ui.confirm')),
                    name: 'import-off-canvas'
                )
        );
    }
}

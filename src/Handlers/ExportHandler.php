<?php

declare(strict_types=1);

namespace MoonShine\Handlers;

use Illuminate\Support\Facades\Storage;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Exceptions\ActionException;
use MoonShine\Jobs\ExportHandlerJob;
use MoonShine\MoonShineUI;
use MoonShine\Notifications\MoonShineNotification;
use MoonShine\Traits\WithStorage;
use OpenSpout\Common\Exception\InvalidArgumentException;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Exception\UnsupportedTypeException;
use OpenSpout\Writer\Exception\WriterNotOpenedException;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ExportHandler extends Handler
{
    use WithStorage;

    protected ?string $icon = 'heroicons.outline.table-cells';

    protected bool $withQuery = true;

    protected bool $isCsv = false;

    protected string $csvDelimiter = ',';

    public function csv(): static
    {
        $this->isCsv = true;

        return $this;
    }

    public function delimiter(string $value): static
    {
        $this->csvDelimiter = $value;

        return $this;
    }

    /**
     * @throws ActionException
     * @throws IOException
     * @throws WriterNotOpenedException
     * @throws UnsupportedTypeException
     * @throws InvalidArgumentException|Throwable
     */
    public function handle(): Response
    {
        if (! $this->hasResource()) {
            throw new ActionException('Resource is required for action');
        }

        $this->resolveStorage();

        $path = Storage::disk($this->getDisk())
            ->path(
                "{$this->getDir()}/{$this->getResource()->uriKey()}." . ($this->isCsv() ? 'csv' : 'xlsx')
            );

        if ($this->isQueue()) {
            ExportHandlerJob::dispatch(
                $this->getResource()::class,
                $path,
                $this->getDisk(),
                $this->getDir(),
                $this->getDelimiter()
            );

            MoonShineUI::toast(
                __('moonshine::ui.resource.queued')
            );

            return back();
        }

        return response()->download(
            self::process(
                $path,
                $this->getResource(),
                $this->getDisk(),
                $this->getDir(),
                $this->getDelimiter()
            )
        );
    }

    public function isCsv(): bool
    {
        return $this->isCsv;
    }

    public function getDelimiter(): string
    {
        return $this->csvDelimiter;
    }

    /**
     * @throws WriterNotOpenedException
     * @throws IOException
     * @throws UnsupportedTypeException
     * @throws InvalidArgumentException|Throwable
     */
    public static function process(
        string $path,
        ResourceContract $resource,
        string $disk = 'public',
        string $dir = '/',
        string $delimiter = ','
    ): string {
        $items = $resource->items();
        $data = collect();

        foreach ($items as $index => $item) {
            $row = [];

            $fields = $resource
                ->getIndexFields()
                ->exportFields()
                ->fillCloned($item->toArray(), $item, $index);

            foreach ($fields as $field) {
                $row[$field->label()] = $field
                    ->rawMode()
                    ->preview();
            }

            $data->add($row);
        }

        $fastExcel = new FastExcel($data);

        if (str($path)->contains('.csv')) {
            $fastExcel->configureCsv($delimiter);
        }

        $result = $fastExcel->export($path);

        $url = str($path)
            ->remove(Storage::disk($disk)->path($dir))
            ->value();

        MoonShineNotification::send(
            trans('moonshine::ui.resource.export.exported'),
            [
                'link' => Storage::disk($disk)->url(trim($dir, '/') . $url),
                'label' => trans('moonshine::ui.download'),
            ]
        );

        return $result;
    }
}

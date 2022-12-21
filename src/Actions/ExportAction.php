<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Actions;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Leeto\MoonShine\Contracts\Actions\ActionContract;
use Leeto\MoonShine\Contracts\Resources\ResourceContract;
use Leeto\MoonShine\Exceptions\ActionException;
use Leeto\MoonShine\Jobs\ExportActionJob;
use Leeto\MoonShine\Models\MoonshineUser;
use Leeto\MoonShine\Notifications\MoonShineDatabaseNotification;
use Leeto\MoonShine\Notifications\MoonShineNotification;
use Leeto\MoonShine\Traits\WithQueue;
use Leeto\MoonShine\Traits\WithStorage;
use OpenSpout\Common\Exception\InvalidArgumentException;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Exception\UnsupportedTypeException;
use OpenSpout\Writer\Exception\WriterNotOpenedException;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportAction extends Action implements ActionContract
{
    use WithStorage;
    use WithQueue;

    protected static string $view = 'moonshine::actions.export';

    protected string $triggerKey = 'exportAction';

    /**
     * @throws ActionException
     * @throws IOException
     * @throws WriterNotOpenedException
     * @throws UnsupportedTypeException
     * @throws InvalidArgumentException
     */
    public function handle(): RedirectResponse|BinaryFileResponse
    {
        if (is_null($this->resource())) {
            throw new ActionException('Resource is required for action');
        }

        $this->resolveStorage();

        $path = Storage::disk($this->getDisk())
            ->path("{$this->getDir()}/{$this->resource()->routeAlias()}.xlsx");

        if ($this->isQueue()) {
            ExportActionJob::dispatch(get_class($this->resource()), $path, $this->getDisk(), $this->getDir());

            return redirect()
                ->back()
                ->with('alert', trans('moonshine::ui.resource.queued'));
        }

        return response()->download(
            self::process($path, $this->resource(), $this->getDisk(), $this->getDir())
        );
    }

    /**
     * @throws WriterNotOpenedException
     * @throws IOException
     * @throws UnsupportedTypeException
     * @throws InvalidArgumentException
     */
    public static function process(
        string $path,
        ResourceContract $resource,
        string $disk = 'public',
        string $dir = '/'
    ): string {
        $fields = $resource->exportFields();

        $items = $resource->query()->get();

        $data = collect([]);

        foreach ($items as $item) {
            $row = [];

            foreach ($fields as $field) {
                $row[$field->label()] = $field->exportViewValue($item);
            }

            $data->add($row);
        }

        $result = (new FastExcel($data))
            ->export($path);

        $url = str($path)
            ->remove(Storage::disk($disk)->path($dir))
            ->value();

        MoonShineNotification::send(
            trans('moonshine::ui.resource.export.exported'),
            ['link' => Storage::disk($disk)->url($url), 'label' => trans('moonshine::ui.download')]
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

        $query = [$this->triggerKey => true];

        if (request()->has('filters')) {
            foreach (request()->query('filters') as $filterField => $filterQuery) {
                if (is_array($filterQuery)) {
                    foreach ($filterQuery as $filterInnerField => $filterValue) {
                        if (is_numeric($filterInnerField) && !is_array($filterValue)) {
                            $query['filters'][$filterField][] = $filterValue;
                        } else {
                            $query['filters'][$filterInnerField] = $filterValue;
                        }
                    }
                } else {
                    $query['filters'][$filterField] = $filterQuery;
                }
            }
        }

        if (request()->has('search')) {
            $query['search'] = request('search');
        }

        return $this->resource()->route('actions', query: $query);
    }

    protected function resolveStorage()
    {
        if (!Storage::disk($this->getDisk())->exists($this->getDir())) {
            Storage::disk($this->getDisk())->makeDirectory($this->getDir());
        }
    }
}

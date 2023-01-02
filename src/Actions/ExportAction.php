<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Actions;

use Illuminate\Support\Facades\Storage;
use Leeto\MoonShine\Http\Requests\Resources\ActionFormRequest;
use OpenSpout\Common\Exception\InvalidArgumentException;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Exception\UnsupportedTypeException;
use OpenSpout\Writer\Exception\WriterNotOpenedException;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @see https://github.com/rap2hpoutre/fast-excel
 */
final class ExportAction extends Action
{
    protected string $disk = 'local';

    protected string $dir = 'moonshine/exports';

    /**
     * @throws WriterNotOpenedException
     * @throws IOException
     * @throws UnsupportedTypeException
     * @throws InvalidArgumentException
     */
    public function handle(ActionFormRequest $request): BinaryFileResponse
    {
        $this->resolveStorage();

        $columns = $request->getResource()
            ->fieldsCollection()
            ->exportFields()
            ->onlyFieldsColumns()
            ->toArray();

        $items = $request->getResource()
            ->resolveQuery()
            ->select($columns)
            ->get();

        $path = Storage::disk($this->getDisk())
            ->path("{$this->getDir()}/export-{$request->getResource()->uriKey()}.xlsx");

        return response()->download(
            (new FastExcel($items))
                ->export($path)
        );
    }

    protected function resolveStorage(): void
    {
        if (!Storage::disk($this->getDisk())->exists($this->getDir())) {
            Storage::disk($this->getDisk())->makeDirectory($this->getDir());
        }
    }

    public function getDisk(): string
    {
        return $this->disk;
    }

    public function disk(string $disk): self
    {
        $this->disk = $disk;

        return $this;
    }

    public function getDir(): string
    {
        return $this->dir;
    }

    public function dir(string $dir): self
    {
        $this->dir = $dir;

        return $this;
    }
}

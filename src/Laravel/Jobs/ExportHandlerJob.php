<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MoonShine\Laravel\Handlers\ExportHandler;
use OpenSpout\Common\Exception\InvalidArgumentException;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Exception\UnsupportedTypeException;
use OpenSpout\Writer\Exception\WriterNotOpenedException;
use Throwable;

class ExportHandlerJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected string $resource,
        protected string $path,
        protected array $query,
        protected string $disk,
        protected string $dir,
        protected string $delimiter = ','
    ) {
    }

    /**
     * @throws IOException
     * @throws WriterNotOpenedException
     * @throws UnsupportedTypeException
     * @throws InvalidArgumentException|Throwable
     */
    public function handle(): void
    {
        ExportHandler::process(
            $this->path,
            new $this->resource(),
            $this->query,
            $this->disk,
            $this->dir,
            $this->delimiter
        );
    }
}

<?php

declare(strict_types=1);

namespace MoonShine\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MoonShine\Handlers\ImportHandler;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Exception\UnsupportedTypeException;
use OpenSpout\Reader\Exception\ReaderNotOpenedException;

class ImportHandlerJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected string $resource,
        protected string $path,
        protected bool $deleteAfter,
        protected string $delimiter = ',',
        protected array $adminsToNotify = []
    ) {
    }

    /**
     * @throws IOException
     * @throws UnsupportedTypeException
     * @throws ReaderNotOpenedException
     */
    public function handle(): void
    {
        ImportHandler::process(
            $this->path,
            new $this->resource(),
            $this->deleteAfter,
            $this->delimiter,
            $this->adminsToNotify
        );
    }
}

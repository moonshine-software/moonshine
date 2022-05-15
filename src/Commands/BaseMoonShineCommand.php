<?php

namespace Leeto\MoonShine\Commands;

use Illuminate\Console\Command;

class BaseMoonShineCommand extends Command
{
    protected string $directory = 'app/MoonShine';

    protected function getStub(string $name): string
    {
        return $this->laravel['files']->get(__DIR__."/../stubs/$name.stub");
    }

    protected function getDirectory(): string
    {
        return config('moonshine.dir', $this->directory);
    }

    protected function makeDir(string $path = ''): void
    {
        if(isset($this->laravel['files'])) {
            $this->laravel['files']->makeDirectory("$this->directory/$path", 0755, true, true);
        }
    }
}

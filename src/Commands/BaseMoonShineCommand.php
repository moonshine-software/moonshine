<?php

namespace Leeto\MoonShine\Commands;

use Illuminate\Console\Command;

class BaseMoonShineCommand extends Command
{
    /**
     * Install directory.
     *
     * @var string
     */
    protected string $directory = 'app/MoonShine';

    /**
     * Get stub contents.
     *
     * @param $name
     *
     * @return string
     */
    protected function getStub($name): string
    {
        return $this->laravel['files']->get(__DIR__."/stubs/$name.stub");
    }

    /**
     * Make new directory.
     *
     * @param string $path
     */
    protected function makeDir(string $path = '')
    {
        $this->laravel['files']->makeDirectory("$this->directory/$path", 0755, true, true);
    }
}

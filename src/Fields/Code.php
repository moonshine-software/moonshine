<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;

class Code extends Textarea
{
    protected static string $view = 'moonshine::fields.code';

    public string $language = 'php';

    public bool $lineNumbers = false;

    protected array $assets = [
        'https://unpkg.com/codeflask/build/codeflask.min.js'
    ];

    public function language(string $language): static
    {
        $this->language = $language;

        return $this;
    }

    public function lineNumbers(): static
    {
        $this->lineNumbers = true;

        return $this;
    }

    public function indexViewValue(Model $item, bool $container = true): string
    {
        return (string) str($item->{$this->field()})
            ->before('<pre>')
            ->after('</pre>')
            ->stripTags();
    }
}

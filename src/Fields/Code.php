<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;

class Code extends Textarea
{
    protected static string $view = 'moonshine::fields.code';

    public string $language = 'php';

    public bool $lineNumbers = false;

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
        return (string) str($item->{$this->column()})
            ->before('<pre>')
            ->after('</pre>')
            ->stripTags();
    }
}

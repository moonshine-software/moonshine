<?php

declare(strict_types=1);

namespace MoonShine\Dashboard;

final class TextBlock extends DashboardItem
{
    protected static string $view = 'moonshine::blocks.text';

    public function __construct(
        string $label,
        protected string $text,
    ) {
        $this->setLabel($label);
    }

    public function text(): string
    {
        return $this->text;
    }

    public function id(string $index = null): string
    {
        return str(uniqid('', true))->slug('_')->value();
    }

    public function name(string $index = null): string
    {
        return $this->id($index);
    }
}

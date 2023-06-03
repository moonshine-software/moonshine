<?php

declare(strict_types=1);

namespace MoonShine\Dashboard;

use MoonShine\Traits\WithUniqueId;

/**
 * @method static static make(string $label, string $text)
 */
final class TextBlock extends DashboardItem
{
    use WithUniqueId;

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
}

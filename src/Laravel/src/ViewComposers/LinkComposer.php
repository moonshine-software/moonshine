<?php

declare(strict_types=1);

namespace MoonShine\Laravel\ViewComposers;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\View\View;
use MoonShine\Support\Stringify;

final class LinkComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();
        $slot = $data['slot'] ?? '';
        $content = $slot instanceof Htmlable ? $slot->toHtml() : Stringify::value($slot);

        if (trim($content) === '') {
            $view->with('slot', $data['labelHtml'] ?? $data['label'] ?? '');
        }
    }
}

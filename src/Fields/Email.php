<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

class Email extends Field
{
    protected static string $view = 'input';

    protected static string $type = 'email';
}

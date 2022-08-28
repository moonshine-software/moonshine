<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Leeto\MoonShine\Contracts\Fields\HasFields;
use Leeto\MoonShine\Contracts\Fields\Removable as RemovableContract;
use Leeto\MoonShine\Traits\Fields\Removable;
use Leeto\MoonShine\Traits\WithFields;
use Throwable;

class Json extends Field implements HasFields, RemovableContract
{
    use WithFields;
    use Removable;

    protected static string $component = 'JsonField';

    protected bool $keyValue = false;

    /**
     * @throws Throwable
     */
    public function keyValue(string $key = 'Key', string $value = 'Value'): static
    {
        $this->keyValue = true;

        $this->fields([
            Text::make($key, 'key'),
            Text::make($value, 'value'),
        ]);

        return $this;
    }

    public function isKeyValue(): bool
    {
        return $this->keyValue;
    }
}

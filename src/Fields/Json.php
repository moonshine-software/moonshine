<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Contracts\Fields\HasFields;
use Leeto\MoonShine\Traits\Fields\WithFields;
use Throwable;

class Json extends Field implements HasFields
{
    use WithFields;

    protected bool $keyValue = false;

    protected bool $group = true;

    public function getView(): string
    {
        return $this->isFullPage() ? 'moonshine::fields.full-fields' : 'moonshine::fields.table-fields';
    }

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

    public function save(Model $item): Model
    {
        if ($this->isKeyValue()) {
            if ($this->requestValue() !== false) {
                $item->{$this->field()} = collect($this->requestValue())
                    ->mapWithKeys(fn($data) => [$data['key'] => $data['value']]);
            }

            return $item;
        }

        return parent::save($item);
    }
}

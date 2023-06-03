<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Traits\WithFields;

final class StackFields extends Field implements HasFields
{
    use WithFields;

    protected bool $fieldContainer = false;

    protected static string $view = 'moonshine::fields.stack';

    public function save(Model $item): Model
    {
        $this->getFields()->onlyFields()->each(static function ($field) use (&$item) {
            $item = $field->save($item);
        });

        return $item;
    }

    public function indexViewValue(Model $item, bool $container = true): string
    {
        return $this->getFields()->indexFields()->implode(function ($field) use ($item, $container) {
            return $field->indexViewValue($item, $container);
        }, '<br>');
    }

    public function afterSave(Model $item): void
    {
        parent::afterSave($item);

        $this->getFields()
            ->onlyFields()
            ->each(fn (FormElement $field) => $field->afterSave($item));
    }
}

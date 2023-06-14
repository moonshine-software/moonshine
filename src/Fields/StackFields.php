<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Traits\WithFields;
use Throwable;

class StackFields extends Field implements HasFields
{
    use WithFields;

    protected static string $view = 'moonshine::fields.stack';
    protected bool $fieldContainer = false;

    protected bool $withLabels = false;

    public function withLabels(): static
    {
        $this->withLabels = true;

        return $this;
    }

    public function hasLabels(): bool
    {
        return $this->withLabels;
    }

    /**
     * @throws Throwable
     */
    public function save(Model $item): Model
    {
        $this->getFields()->onlyFields()->each(
            static function ($field) use (&$item): void {
                $item = $field->save($item);
            }
        );

        return $item;
    }

    public function indexViewValue(Model $item, bool $container = true): string
    {
        return view($this->getView(), [
            'element' => $this,
            'item' => $item,
            'indexView' => true,
        ])->render();
    }

    /**
     * @throws Throwable
     */
    public function afterSave(Model $item): void
    {
        parent::afterSave($item);

        $this->getFields()
            ->onlyFields()
            ->each(fn (FormElement $field) => $field->afterSave($item));
    }
}

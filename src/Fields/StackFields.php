<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Traits\WithFields;
use Throwable;

class StackFields extends Field implements HasFields
{
    use WithFields;

    protected string $view = 'moonshine::fields.stack';

    protected bool $withWrapper = false;

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

    public function resolveFill(
        array $raw = [],
        mixed $casted = null,
        int $index = 0
    ): Field {
        $this->getFields()
            ->onlyFields()
            ->each(fn (Field $field) => $field->resolveFill($raw, $casted, $index));

        return $this;
    }

    protected function resolveOnApply(): ?Closure
    {
        return function ($item) {
            $this->getFields()->onlyFields()->each(
                static function (Field $field) use ($item): void {
                    $field->apply(
                        static function (mixed $item) use ($field): mixed {
                            if ($field->requestValue() !== false) {
                                data_set($item, $field->column(), $field->requestValue());
                            }

                            return $item;
                        },
                        $item
                    );
                }
            );

            return $item;
        };
    }

    protected function resolvePreview(): string
    {
        return view($this->getView(), [
            'element' => $this,
            'indexView' => true,
        ])->render();
    }

    /**
     * @throws Throwable
     */
    protected function resolveBeforeApply(mixed $data): mixed
    {
        $this->getFields()
            ->onlyFields()
            ->each(fn (Field $field): mixed => $field->beforeApply($data));

        return $data;
    }

    /**
     * @throws Throwable
     */
    protected function resolveAfterApply(mixed $data): mixed
    {
        $this->getFields()
            ->onlyFields()
            ->each(fn (Field $field): mixed => $field->afterApply($data));

        return $data;
    }

    /**
     * @throws Throwable
     */
    protected function resolveAfterDestroy(mixed $data): mixed
    {
        $this->getFields()
            ->onlyFields()
            ->each(fn (Field $field): mixed => $field->afterDestroy($data));

        return $data;
    }

    public function __clone()
    {
        foreach ($this->fields as $index => $field) {
            $this->fields[$index] = clone $field;
        }
    }

}

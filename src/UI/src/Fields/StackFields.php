<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\HasFieldsContract;
use MoonShine\UI\Collections\Fields;
use MoonShine\UI\Components\FieldsGroup;
use MoonShine\UI\Contracts\FieldsWrapperContract;
use MoonShine\UI\Traits\WithFields;
use Throwable;

/**
 * @implements  HasFieldsContract<Fields|FieldsContract>
 */
class StackFields extends Field implements HasFieldsContract, FieldsWrapperContract
{
    use WithFields;

    protected string $view = 'moonshine::fields.stack';

    /**
     * @throws Throwable
     */
    protected function resolveFill(
        array $raw = [],
        ?DataWrapperContract $casted = null,
        int $index = 0
    ): static {
        return $this
            ->setRawValue($raw)
            ->setData($casted)
            ->setRowIndex($index);
    }

    /**
     * @throws Throwable
     */
    protected function resolvePreview(): Renderable|string
    {
        return FieldsGroup::make(
            $this->getFields()
        )
            ->mapFields(
                fn (FieldContract $field, int $index): FieldContract => $field
                ->fillData($this->getData())
                ->previewMode()
            )
            ->render();
    }

    protected function resolveOnApply(): ?Closure
    {
        return function ($item) {
            $this->getFields()->onlyFields()->each(
                static function (FieldContract $field) use ($item): void {
                    $field->apply(
                        static function (mixed $item) use ($field): mixed {
                            if ($field->getRequestValue() !== false) {
                                data_set($item, $field->getColumn(), $field->getRequestValue());
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

    /**
     * @throws Throwable
     */
    protected function resolveBeforeApply(mixed $data): mixed
    {
        $this->getFields()
            ->onlyFields()
            ->each(static fn (FieldContract $field): mixed => $field->beforeApply($data));

        return $data;
    }

    /**
     * @throws Throwable
     */
    protected function resolveAfterApply(mixed $data): mixed
    {
        $this->getFields()
            ->onlyFields()
            ->each(static fn (FieldContract $field): mixed => $field->afterApply($data));

        return $data;
    }

    /**
     * @throws Throwable
     */
    protected function resolveAfterDestroy(mixed $data): mixed
    {
        $this->getFields()
            ->onlyFields()
            ->each(
                static fn (FieldContract $field): mixed => $field
                ->fillData($data)
                ->afterDestroy($data)
            );

        return $data;
    }

    /**
     * @throws Throwable
     */
    protected function viewData(): array
    {
        return [
            'fields' => $this->getFields(),
        ];
    }

    /**
     * @throws Throwable
     */
    public function __clone()
    {
        if (! $this->fields instanceof Closure) {
            $fields = [];

            foreach ($this->getRawFields() as $index => $field) {
                $fields[$index] = clone $field;
            }

            $this->fields($fields);
        }
    }
}

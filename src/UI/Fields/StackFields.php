<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\HasFieldsContract;
use MoonShine\UI\Components\FieldsGroup;
use MoonShine\UI\Components\Layout\LineBreak;
use MoonShine\UI\Contracts\FieldsWrapperContract;
use MoonShine\UI\Traits\WithFields;
use Throwable;

class StackFields extends Field implements HasFieldsContract, FieldsWrapperContract
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

    /**
     * @throws Throwable
     */
    protected function resolveFill(
        array $raw = [],
        mixed $casted = null,
        int $index = 0
    ): static {
        $this->getFields()
            ->onlyFields()
            ->each(static fn (FieldContract $field): FieldContract => $field->fillData(is_null($casted) ? $raw : $casted, $index));

        return $this;
    }

    /**
     * @throws Throwable
     */
    protected function resolvePreview(): Renderable|string
    {
        return FieldsGroup::make(
            $this->getFields()->indexFields()
        )
            ->mapFields(fn (FieldContract $field): FieldContract => $field
                ->beforeRender(fn (): string => $this->hasLabels() ? '' : (string) LineBreak::make())
                ->withoutWrapper($this->hasLabels())
                ->previewMode())
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
    public function __clone()
    {
        $fields = [];

        foreach ($this->getRawFields() as $index => $field) {
            $fields[$index] = clone $field;
        }

        $this->fields($fields);
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
}

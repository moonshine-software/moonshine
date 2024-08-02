<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use MoonShine\Contracts\Core\TypeCasts\CastedDataContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\UI\Components\FieldsGroup;
use MoonShine\UI\Components\Layout\LineBreak;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

/**
 * @method static static make(Closure|string $label, ?Closure $fields = null)
 */
class Td extends Template
{
    private ?Closure $conditionalFields = null;

    private ?Closure $tdAttributes = null;

    protected bool $withWrapper = false;

    protected bool $withLabels = false;

    public function __construct(Closure|string $label, ?Closure $fields = null)
    {
        parent::__construct($label);

        $this->conditionalFields($fields);
        $this->previewMode();
    }

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
     * @param  ?Closure(mixed $data, static $ctx): static $fields
     */
    public function conditionalFields(?Closure $fields = null): static
    {
        $this->conditionalFields = $fields;

        return $this;
    }

    public function hasConditionalFields(): bool
    {
        return ! is_null($this->conditionalFields);
    }

    public function getConditionalFields(): array
    {
        return value($this->conditionalFields, $this->getData()?->getOriginal(), $this);
    }

    protected function resolveFill(
        array $raw = [],
        ?CastedDataContract $casted = null,
        int $index = 0
    ): static {
        return $this
            ->setRawValue($raw)
            ->setData($casted)
            ->setRowIndex($index);
    }

    /**
     * @param  Closure(mixed $data, static $ctx): array  $attributes
     */
    public function tdAttributes(Closure $attributes): static
    {
        $this->tdAttributes = $attributes;

        return $this;
    }

    public function hasTdAttributes(): bool
    {
        return ! is_null($this->tdAttributes);
    }

    public function resolveTdAttributes(mixed $data): array
    {
        return $this->hasTdAttributes()
            ? value($this->tdAttributes, $data, $this)
            : [];
    }

    protected function resolveRawValue(): mixed
    {
        return '';
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Throwable
     */
    protected function resolvePreview(): string|Renderable
    {
        $fields = $this->hasConditionalFields()
            ? $this->getConditionalFields()
            : $this->getFields();

        return FieldsGroup::make($this->getCore()->getFieldsCollection($fields))
            ->mapFields(fn (FieldContract $field, int $index): FieldContract => $field
                ->fillData($this->getData())
                ->beforeRender(fn (): string => $this->hasLabels() || $index === 0 ? '' : (string) LineBreak::make())
                ->withoutWrapper($this->hasLabels())
                ->previewMode())
            ->render();
    }

    public function render(): string
    {
        return $this->preview();
    }
}

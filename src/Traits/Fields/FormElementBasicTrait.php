<?php


namespace Leeto\MoonShine\Traits\Fields;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Contracts\Fields\FieldHasRelationContract;
use Leeto\MoonShine\Contracts\Resources\ResourceContract;

trait FormElementBasicTrait
{
    protected string $label = '';

    protected string $field;

    protected string|null $relation = null;

    protected string|null $name = null;

    protected string|null $id = null;

    protected ResourceContract|null $resource;

    protected string $resourceTitleField = '';

    protected static string $type = '';

    protected static string $view = '';

    protected string|null $default = null;

    protected bool $required = false;

    protected bool $disabled = false;

    protected bool $nullable = false;

    protected bool $multiple = false;

    protected bool $readonly = false;

    protected string $autocomplete = 'off';

    protected string $mask = '';

    protected array $options = [];

    public static function make(...$arguments): static
    {
        return new static(...$arguments);
    }

    final public function __construct(string $label = null, string $field = null, ResourceContract|string|null $resource = null)
    {
        $this->setLabel($label ?? str($this->label)->ucfirst());
        $this->setField($field ?? str($this->label)->lower()->snake());

        if($this instanceof FieldHasRelationContract) {
            if(!$this->isRelationToOne() && !$this->isRelationHasOne()) {
                $this->multiple();
            }

            $this->setField($field ?? str($this->label)->camel());

            if(($this->isRelationToOne() && !$this->isRelationHasOne()) && !str($this->field())->contains('_id')) {
                $this->setField(
                    str($this->field())
                        ->append('_id')
                        ->snake()
                );
            }

            $this->setRelation($field ?? str($this->label)->camel());

            if(str($this->relation())->contains('_id')) {
                $this->setRelation(
                    str($this->relation())
                        ->remove('_id')
                        ->camel()
                );
            }

            if($resource instanceof ResourceContract) {
                $this->setResource($resource);
            } elseif(is_string($resource)) {
                $this->setResourceTitleField($resource);
            }
        }
    }

    public function id(string $index = null): string
    {
        if($this->id) {
            return $this->id;
        }

        return (string) str($this->name ?? $this->name())
            ->remove(['[', ']'])
            ->snake()
            ->when(!is_null($index), fn(Stringable $str) => $str->append("_$index"));
    }

    public function name(string $index = null): string
    {
        return $this->prepareName($index);
    }

    protected function prepareName($index = null, $wrap = null): string
    {
        if($this->name) {
            return $this->name;
        }

        return (string) str($this->field())
            ->when(!is_null($wrap), fn(Stringable $str) => $str->wrap("{$wrap}[", "]"))
            ->when(
                $this->isMultiple(),
                fn(Stringable $str) => $str->append("[".($index ?? '')."]")
            );
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function setId(string $id): static
    {
        $this->id = str($id)->remove(['[', ']'])->snake();

        return $this;
    }

    public function field(): string
    {
        return $this->field;
    }

    public function setField(string $field): static
    {
        $this->field = $field;

        return $this;
    }

    public function relation(): string|null
    {
        return $this->relation;
    }

    public function setRelation(string $relation): static
    {
        $this->relation = $relation;

        return $this;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function type(): string
    {
        return static::$type;
    }

    public function getView(): string
    {
        return static::$view;
    }

    public function attributes(): array
    {
        return get_object_vars($this);
    }

    public function getAttribute(string $name): mixed
    {
        return collect($this->attributes())->get($name);
    }

    public function meta(): string
    {
        $meta = str('');

        if($this->xModel) {
            $meta = $meta->append(" x-model='{$this->xModelField()}' ");
            $meta = $meta->append(" x-bind:name=`{$this->name()}` ");
            $meta = $meta->append(" x-bind:id=`{$this->id()}` ");
        }

        return (string) $meta;
    }

    public function resource(): ResourceContract|null
    {
        return $this->resource ?? $this->findResource();
    }

    protected function findResource(): ResourceContract|null
    {
        $resourceClass = (string) str('App\MoonShine\Resources\\')
            ->append(str($this->relation() ?? $this->field())->studly()->singular())
            ->append('Resource');

        return class_exists($resourceClass) ? new $resourceClass() : null;
    }

    public function setResource(ResourceContract|null $resource): void
    {
        $this->resource = $resource;
    }

    public function resourceTitleField(): string
    {
        if($this->resourceTitleField) {
            return $this->resourceTitleField;
        }
        return $this->resource() && $this->resource()->titleField()
            ? $this->resource()->titleField()
            : 'id';
    }

    public function setResourceTitleField(string $resourceTitleField): static
    {
        $this->resourceTitleField = $resourceTitleField;

        return $this;
    }

    public function required(): static
    {
        $this->required = true;

        return $this;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function default(string $default): static
    {
        $this->default = $default;

        return $this;
    }

    public function getDefault(): string|null
    {
        return old($this->nameDot(), $this->default);
    }

    public function disabled(): static
    {
        $this->disabled = true;

        return $this;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function getMask(): string
    {
        return $this->mask;
    }

    public function mask(string $mask): static
    {
        $this->mask = $mask;

        return $this;
    }

    public function readonly(): static
    {
        $this->readonly = true;

        return $this;
    }

    public function isReadonly(): bool
    {
        return $this->readonly;
    }

    public function nullable(): static
    {
        $this->nullable = true;

        return $this;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function multiple(): static
    {
        $this->multiple = true;

        return $this;
    }

    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    public function options(array $data): static
    {
        $this->options = $data;

        return $this;
    }

    public function autocomplete(string $value): static
    {
        $this->autocomplete = $value;

        return $this;
    }

    public function getAutocomplete(): string
    {
        return $this->autocomplete;
    }

    public function values(): array
    {
        return $this->options;
    }

    public function isChecked(Model $item, string $value): bool
    {
        $formValue = $this->formViewValue($item);

        if($formValue instanceof Collection) {
            return $this->formViewValue($item)->contains("id", "=", $value);
        }

        if(is_array($formValue)) {
            return in_array($value, $formValue);
        }

        return false;
    }

    public function isSelected(Model $item, string $value): bool
    {
        return (string) $this->formViewValue($item) === $value
            || (!$this->formViewValue($item) && (string) $this->getDefault() === $value);
    }

    public function requestValue(): mixed
    {
        return request(
            $this->nameDot(),
            $this->getDefault() ?? old($this->nameDot(), false)
        );
    }

    protected function nameDot(): string
    {
        $name = (string) str($this->name())->replace('[]', '');

        parse_str($name, $array);

        $result = collect(Arr::dot(array_filter($array)));


        return $result->isEmpty()
            ? $name
            : (string) str($result->keys()->first());
    }
}
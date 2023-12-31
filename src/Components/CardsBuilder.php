<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\Traits\HasAsync;
use MoonShine\Traits\WithColumnSpan;
use Throwable;

/**
 * @method static static make(Paginator|iterable $items = [], Fields|array $fields = [])
 */
final class CardsBuilder extends IterableComponent
{
    use HasAsync;
    use WithColumnSpan;

    protected string $view = 'moonshine::components.cards';

    protected array $components = [];

    protected Closure|string $title = '';

    protected Closure|string $subtitle = '';

    protected Closure|string $thumbnail = '';

    protected Closure|string $url = '';

    protected Closure|string $content = '';

    protected Closure|string $header = '';

    protected bool $overlay = false;

    protected ?Closure $customComponent = null;

    public function __construct(
        Paginator|iterable $items = [],
        Fields|array $fields = [],
    ) {
        $this->items($items);
        $this->fields($fields);
        $this->columnSpan(4);

        $this->withAttributes([]);
    }

    public function title(Closure|string $value): self
    {
        $this->title = $value;

        return $this;
    }

    public function subtitle(Closure|string $value): self
    {
        $this->subtitle = $value;

        return $this;
    }

    public function thumbnail(Closure|string $value): self
    {
        $this->thumbnail = $value;

        return $this;
    }

    public function url(Closure|string $value): self
    {
        $this->url = $value;

        return $this;
    }

    public function overlay(): self
    {
        $this->overlay = true;

        return $this;
    }

    public function content(Closure|string $value): self
    {
        $this->content = $value;

        return $this;
    }

    public function header(Closure|string $value): self
    {
        $this->header = $value;

        return $this;
    }

    protected function prepareAsyncUrl(?string $asyncUrl = null): ?string
    {
        return $asyncUrl ?? moonshineRouter()
            ->asyncComponent(name: $this->getName());
    }

    /**
     * @throws Throwable
     */
    public function components(): Collection
    {
        $fields = $this->getFields();

        return $this->getItems()->filter()->map(function (mixed $data, int $index) use ($fields) {
            $casted = $this->castData($data);
            $raw = $this->unCastData($data);

            $fields = $this->getFilledFields($raw, $casted, $index, $fields);

            if(!is_null($this->customComponent)) {
                return value($this->customComponent, $data, $index, $this);
            }

            return Card::make(...$this->getMapper($data, $fields, $index))
                ->content((string) value($this->content, $data, $index, $this))
                ->header((string) value($this->header, $data, $index, $this))
                ->actions(
                    fn () => ActionGroup::make($this->getButtons($data)->toArray())
                );
        });
    }

    public function customComponent(Closure $component): self
    {
        $this->customComponent = $component;

        return $this;
    }

    protected function getMapperValue(string $column, mixed $data, int $index): string
    {
        return is_string($this->{$column})
            ? data_get($data, $this->{$column}, '')
            : value($this->{$column}, $data, $index, $this);
    }

    protected function getMapper(mixed $data, Fields $fields, int $index): array
    {
        $values = $fields->values()
            ->mapWithKeys(fn (Field $value): array => [$value->label() => (string) $value->preview()])
            ->toArray();

        return [
            'title' => $this->getMapperValue('title', $data, $index),
            'subtitle' => $this->getMapperValue('subtitle', $data, $index),
            'thumbnail' => $this->getMapperValue('thumbnail', $data, $index),
            'url' => $this->getMapperValue('url', $data, $index),
            'overlay' => $this->overlay,
            'values' => $values,
        ];
    }

    /**
     * @throws Throwable
     */
    protected function viewData(): array
    {
        if ($this->isAsync() && $this->hasPaginator()) {
            $this->getPaginator()
                ?->appends(request()->except('page'))
                ?->setPath($this->prepareAsyncUrlFromPaginator());
        }

        return [
            'components' => $this->components(),
            'name' => $this->getName(),
            'hasPaginator' => $this->hasPaginator(),
            'simplePaginate' => ! $this->getPaginator() instanceof LengthAwarePaginator,
            'paginator' => $this->getPaginator(),
            'async' => $this->isAsync(),
            'asyncUrl' => $this->asyncUrl(),
            'colSpan' => $this->columnSpanValue(),
            'adaptiveColSpan' => $this->adaptiveColumnSpanValue(),
        ];
    }
}

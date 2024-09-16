<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Closure;
use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\HasAsyncContract;
use MoonShine\Contracts\UI\HasFieldsContract;
use MoonShine\UI\Collections\Fields;
use MoonShine\UI\Traits\Components\WithColumnSpan;
use MoonShine\UI\Traits\HasAsync;
use MoonShine\UI\Traits\WithFields;
use Throwable;

/**
 * @method static static make(iterable $items = [], FieldsContract|array $fields = [])
 *
 * @implements HasFieldsContract<Fields|FieldsContract>
 */
final class CardsBuilder extends IterableComponent implements
    HasFieldsContract,
    HasAsyncContract
{
    use WithFields;
    use HasAsync;
    use WithColumnSpan;

    protected string $view = 'moonshine::components.cards';

    protected array $translates = [
        'notfound' => 'moonshine::ui.notfound',
    ];

    protected array $components = [];

    /**
     * @var (Closure(mixed, int, self): string)|string
     */
    protected Closure|string $title = '';

    /**
     * @var (Closure(mixed, int, self): string)|string
     */
    protected Closure|string $subtitle = '';

    /**
     * @var (Closure(mixed, int, self): string)|string
     */
    protected Closure|string $thumbnail = '';

    /**
     * @var (Closure(mixed, int, self): string)|string
     */
    protected Closure|string $url = '';

    /**
     * @var (Closure(mixed, int, self): string)|string
     */
    protected Closure|string $content = '';

    /**
     * @var (Closure(mixed, int, self): string)|string
     */
    protected Closure|string $header = '';

    protected bool $overlay = false;

    /**
     * @var ?Closure(mixed, int, self): ComponentContract
     */
    protected ?Closure $customComponent = null;

    /**
     * @var (Closure(mixed, int, self): array)|array
     */
    protected array|Closure $componentAttributes = [];

    public function __construct(
        iterable $items = [],
        FieldsContract|array $fields = [],
    ) {
        parent::__construct();

        $this->items($items);
        $this->fields($fields);
        $this->columnSpan(4);

        $this->withAttributes([]);
    }

    /**
     * @param (Closure(mixed $data, int $index, self $ctx): string)|string $value
     */
    public function title(Closure|string $value): self
    {
        $this->title = $value;

        return $this;
    }

    /**
     * @param (Closure(mixed $data, int $index, self $ctx): string)|string $value
     */
    public function subtitle(Closure|string $value): self
    {
        $this->subtitle = $value;

        return $this;
    }

    /**
     * @param (Closure(mixed $data, int $index, self $ctx): string)|string $value
     */
    public function thumbnail(Closure|string $value): self
    {
        $this->thumbnail = $value;

        return $this;
    }

    /**
     * @param (Closure(mixed $data, int $index, self $ctx): string)|string $value
     */
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

    /**
     * @param (Closure(mixed $data, int $index, self $ctx): string)|string $value
     */
    public function content(Closure|string $value): self
    {
        $this->content = $value;

        return $this;
    }

    /**
     * @param (Closure(mixed $data, int $index, self $ctx): string)|string $value
     */
    public function header(Closure|string $value): self
    {
        $this->header = $value;

        return $this;
    }

    protected function prepareAsyncUrl(Closure|string|null $url = null): Closure|string|null
    {
        return $url ?? fn (): string => $this->getCore()->getRouter()->getEndpoints()->component(name: $this->getName());
    }

    /**
     * @param (Closure(mixed $data, int $index, self $ctx): array)|array $attributes
     */
    public function componentAttributes(array|Closure $attributes): self
    {
        $this->componentAttributes = $attributes;

        return $this;
    }

    /**
     * @param Closure(mixed $data, int $index, self $ctx): ComponentContract $component
     */
    public function customComponent(Closure $component): self
    {
        $this->customComponent = $component;

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function getComponents(): Collection
    {
        $fields = $this->getPreparedFields();

        return $this->getItems()->map(function (mixed $data, int $index) use ($fields) {
            $casted = $this->castData($data);

            $fields = $this->getFilledFields($casted->toArray(), $casted, $index, $fields);

            if (! is_null($this->customComponent)) {
                return call_user_func($this->customComponent, $data, $index, $this);
            }

            $buttons = $this->getButtons($casted);

            return Card::make(...$this->getMapper($data, $fields, $index))
                ->content((string) value($this->content, $data, $index, $this))
                ->header((string) value($this->header, $data, $index, $this))
                ->customAttributes(value($this->componentAttributes, $data, $index, $this))
                ->when(
                    $buttons->isNotEmpty(),
                    static fn (Card $card): Card => $card->actions(
                        static fn () => ActionGroup::make($buttons->toArray())
                    )
                );
        });
    }

    protected function getMapperValue(string $column, mixed $data, int $index): string|array
    {
        return is_string($this->{$column})
            ? data_get($data, $this->{$column}, '')
            : value($this->{$column}, $data, $index, $this);
    }

    protected function getMapper(mixed $data, FieldsContract $fields, int $index): array
    {
        $values = $fields->values()
            ->mapWithKeys(static fn (FieldContract $value): array => [$value->getLabel() => (string) $value->preview()])
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

    protected function prepareBeforeRender(): void
    {
        parent::prepareBeforeRender();

        $this->performBeforeRender();
    }

    protected function performBeforeRender(): self
    {
        $this->resolvePaginator();

        if ($this->isAsync() && $this->hasPaginator()) {
            $this->paginator(
                $this->getPaginator()
                    ?->setPath($this->prepareAsyncUrlFromPaginator())
            );
        }

        if ($this->isAsync()) {
            $this->customAttributes([
                'data-events' => $this->getAsyncEvents(),
            ]);
        }

        return $this;
    }

    /**
     * @return array<string, mixed>
     * @throws Throwable
     */
    protected function viewData(): array
    {
        return [
            'components' => $this->getComponents(),
            'name' => $this->getName(),
            'hasPaginator' => $this->hasPaginator(),
            'paginator' => $this->getPaginator(
                $this->isAsync()
            ),
            'async' => $this->isAsync(),
            'asyncUrl' => $this->getAsyncUrl(),
            'colSpan' => $this->getColumnSpanValue(),
            'adaptiveColSpan' => $this->getAdaptiveColumnSpanValue(),
        ];
    }
}

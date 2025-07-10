<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

/** @method static static make(array $items = []) */
final class Breadcrumbs extends MoonShineComponent
{
    protected string $view = 'moonshine::components.breadcrumbs';

    /**
     * @param  array<string, string|array{url: string, title: string, icon: string|null}>  $items
     */
    public function __construct(
        public array $items = [],
    ) {
        parent::__construct();
    }

    public function prepend(string $link, string $label = '', ?string $icon = null): self
    {
        $this->items = (new Collection($this->items))
            ->prepend($this->addItem($label, $icon), $link)
            ->toArray();

        return $this;
    }

    public function add(string $link, string $label = '', ?string $icon = null): self
    {
        $this->items = (new Collection($this->items))
            ->put($link, $this->addItem($label, $icon))
            ->toArray();

        return $this;
    }

    private function addItem(string $label, ?string $icon = null): string
    {
        return Str::of($label)
            ->when(
                $icon,
                static fn (Stringable $str) => $str->append(":::$icon")
            )
            ->value();
    }

    protected function prepareBeforeRender(): void
    {
        parent::prepareBeforeRender();

        $this->items = (new Collection($this->items))->mapWithKeys(static fn (?string $title, string $url): array => [
            $url => [
                'url' => $url,
                'title' => Str::of($title)->before(':::'),
                'icon' => Str::of($title)->contains(':::') ? Str::of($title)->after(':::')->value() : null,
            ],
        ])->toArray();
    }
}

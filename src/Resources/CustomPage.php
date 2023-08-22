<?php

declare(strict_types=1);

namespace MoonShine\Resources;

use MoonShine\Traits\WithLabel;

abstract class CustomPage
{
    use WithLabel;

    protected string $layout = 'moonshine::layouts.app';

    protected bool $withTitle = true;

    public static string $title = '';

    public static string $alias = '';

    public static string $view = '';

    protected array $breadcrumbs = [];

    public function __construct()
    {
        $this->setLabel($this->title());
    }

    public function title(): string
    {
        return static::$title;
    }

    public function alias(): string
    {
        return static::$alias;
    }

    public function layout(string $layout): self
    {
        $this->layout = $layout;

        return $this;
    }

    public function getLayout(): string
    {
        return $this->layout;
    }

    public function withoutTitle(): self
    {
        $this->withTitle = false;

        return $this;
    }

    public function withTitle(): bool
    {
        return $this->withTitle;
    }

    public function breadcrumbs(array $breadcrumbs): self
    {
        $this->breadcrumbs = $breadcrumbs;

        return $this;
    }

    public function getBreadcrumbs(): array
    {
        return collect($this->breadcrumbs)
            ->merge(['#' => $this->label()])
            ->toArray();
    }

    public function getView(): string
    {
        return static::$view;
    }

    /**
     * Get an array of datas
     *
     * @return array<mixed>
     */
    abstract public function datas(): array;

    public function getViewData(): array
    {
        return $this->datas();
    }

    public function url(): string
    {
        return route(
            (string) str('moonshine')
                ->append('.')
                ->append('custom_page'),
            static::$alias
        );
    }
}

<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

/** @method static static make(int $value, int $min = 1, int $max = 5) */
final class Rating extends MoonShineComponent
{
    protected string $view = 'moonshine::components.rating';

    public function __construct(
        public int $value,
        public int $min = 1,
        public int $max = 5
    ) {
        parent::__construct();
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        $filled = null;
        $empty = null;
        $stars = [];

        // Every star has one of two states, so each icon is rendered once and reused.
        for ($star = $this->min; $star <= $this->max; $star++) {
            $stars[] = $star <= $this->value
                ? $filled ??= (string) Icon::make('s.star', 4)->class('text-primary')
                : $empty ??= (string) Icon::make('star', 4)->class('text-secondary');
        }

        return [
            'stars' => $stars,
        ];
    }

    protected function prepareViewData(array $data): array
    {
        return [
            'wrapperAttributes' => $this->getAttributes()
                ->except(['value', 'min', 'max'])
                ->merge(['class' => 'flex items-center gap-x-1']),
        ];
    }
}

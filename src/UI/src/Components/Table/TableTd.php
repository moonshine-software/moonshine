<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Table;

use Closure;
use MoonShine\UI\Components\MoonShineComponent;
use MoonShine\UI\Traits\Components\WithSlotContent;

/**
 * @method static static make(Closure|string $content, ?int $index = null)
 */
class TableTd extends MoonShineComponent
{
    use WithSlotContent;

    protected string $view = 'moonshine::components.table.td';

    public function __construct(
        Closure|string $content,
        protected ?int $index = null
    ) {
        parent::__construct();

        $this->content($content);
    }

    public function getIndex(): int
    {
        return $this->index ?? 0;
    }

    protected function viewData(): array
    {
        return [
            'slot' => $this->getSlot(),
        ];
    }
}

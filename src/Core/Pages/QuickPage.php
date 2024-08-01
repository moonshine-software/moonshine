<?php

declare(strict_types=1);

namespace MoonShine\Core\Pages;

use MoonShine\Support\Traits\Makeable;
use MoonShine\UI\Components\FlexibleRender;

/**
 * Used for rendering and response.
 * Note: Not used in MenuManager and not registered with the ServiceProvider
 *
 * @method static static make(string $title = '')
 */
final class QuickPage extends Page
{
    use Makeable;

    public function __construct(string $title = '')
    {
        $this->title($title);

        parent::__construct(
            $this->getCore(),
        );
    }

    public function components(): array
    {
        return [];
    }

    public function setContentView(string $path, array $data = []): self
    {
        $this->setComponents([
            FlexibleRender::make(
                $this->getCore()->getRenderer()->render($path, $data)
            ),
        ]);

        return $this;
    }
}

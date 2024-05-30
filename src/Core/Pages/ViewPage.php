<?php

declare(strict_types=1);

namespace MoonShine\Core\Pages;

final class ViewPage extends Page
{
    public function components(): array
    {
        return [];
    }

    public function setContentView(string $path, array $data = []): self
    {
        $this->view = $path;
        $this->customViewData = $data;

        return $this;
    }
}

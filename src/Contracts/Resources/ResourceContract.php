<?php

namespace Leeto\MoonShine\Contracts\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Leeto\MoonShine\Contracts\Components\ViewComponentContract;

interface ResourceContract
{
    public function title(): string;

    public function titleField(): string;

    public function getModel(): Model;

    public function getItem(): Model;

    public function getActions(): Collection;

    public function isWithPolicy(): bool;

    public function getFields(): Collection;

    public function tabs(): Collection;

    public function indexFields(): Collection;

    public function exportFields(): Collection;

    public function formFields(): Collection;

    public function getAssets(string $type): array;

    public function extensions($name, Model $item): string;

    public function renderDecoration(ViewComponentContract $decoration, Model $item);

    public function renderField(ViewComponentContract $field, Model $item);

    public function renderFilter(ViewComponentContract $field, Model $item);
}
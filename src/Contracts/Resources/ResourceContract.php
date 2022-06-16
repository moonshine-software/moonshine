<?php

namespace Leeto\MoonShine\Contracts\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Leeto\MoonShine\Contracts\RenderableContract;

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

    public function can(string $ability, Model $item = null): bool;

    public function renderDecoration(RenderableContract $decoration, Model $item);

    public function renderField(RenderableContract $field, Model $item);

    public function renderFilter(RenderableContract $field, Model $item);

    public function renderMetric(RenderableContract $field);
}
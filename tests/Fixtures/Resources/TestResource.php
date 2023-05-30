<?php

namespace MoonShine\Tests\Fixtures\Resources;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Actions\FiltersAction;
use MoonShine\MoonShine;
use MoonShine\Resources\Resource;

class TestResource extends Resource
{
    private array $testRules = [];

    private array $testFields = [];

    private array $testValidationMessages = [];

    private array $testFilters = [];

    private array $testActions = [];

    private array $testBulkActions = [];

    private array $testFormActions = [];

    private array $testItemActions = [];

    private array $testSearch = [];

    private array $testQueryTags = [];

    private ?string $testUriKey = null;

    public function setTestPolicy(bool $value): static
    {
        static::$withPolicy = $value;

        return $this;
    }

    public function setTestTitle(string $value): static
    {
        static::$title = $value;

        return $this;
    }

    public function setTestModel(string $model): static
    {
        static::$model = $model;

        return $this;
    }

    public function setTestRules(array $testRules): static
    {
        $this->testRules = $testRules;

        return $this;
    }

    public function setTestFields(array $testFields): static
    {
        $this->testFields = $testFields;

        return $this;
    }

    public function setTestValidationMessages(array $testValidationMessages): static
    {
        $this->testValidationMessages = $testValidationMessages;

        return $this;
    }

    public function setTestFilters(array $testFilters): static
    {
        $this->testFilters = $testFilters;

        return $this;
    }

    public function setTestActions(array $testActions): static
    {
        $this->testActions = $testActions;

        return $this;
    }

    public function setTestBulkActions(array $testActions): static
    {
        $this->testBulkActions = $testActions;

        return $this;
    }

    public function setTestFormActions(array $testActions): static
    {
        $this->testFormActions = $testActions;

        return $this;
    }

    public function setTestItemActions(array $testActions): static
    {
        $this->testItemActions = $testActions;

        return $this;
    }

    public function setTestSearch(array $testSearch): static
    {
        $this->testSearch = $testSearch;

        return $this;
    }

    public function setTestQueryTags(array $testQueryTags): static
    {
        $this->testQueryTags = $testQueryTags;

        return $this;
    }

    public function setTestRouteAfterSave(string $value): static
    {
        $this->routeAfterSave = $value;

        return $this;
    }

    public function setTestUriKey(string $value): static
    {
        $this->testUriKey = $value;

        return $this;
    }

    public function addRoutes(): static
    {
        $menu = MoonShine::getMenu();

        MoonShine::menu(
            $menu->push($this)->toArray()
        );

        return $this;
    }

    public function rules(Model $item): array
    {
        return $this->testRules;
    }

    public function fields(): array
    {
        return $this->testFields;
    }

    public function validationMessages(): array
    {
        return $this->testValidationMessages;
    }

    public function filters(): array
    {
        if (empty($this->testFilters)) {
            return ['id'];
        }

        return $this->testFilters;
    }

    public function actions(): array
    {
        if (empty($this->testActions)) {
            return [
                FiltersAction::make(trans('moonshine::ui.filters')),
            ];
        }

        return $this->testActions;
    }

    public function bulkActions(): array
    {
        return $this->testBulkActions;
    }

    public function formActions(): array
    {
        return $this->testFormActions;
    }

    public function itemActions(): array
    {
        return $this->testItemActions;
    }

    public function queryTags(): array
    {
        return $this->testQueryTags;
    }

    public function search(): array
    {
        return $this->testSearch;
    }

    public function uriKey(): string
    {
        if ($this->testUriKey) {
            return $this->testUriKey;
        }

        return parent::uriKey();
    }
}

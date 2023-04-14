<?php

namespace MoonShine\Resources\TestResource;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Actions\FiltersAction;
use MoonShine\Resources\Resource;

class MoonshineTestResource extends Resource
{
    private array $testRules = [];

    private array $testFields = [];

    private array $testFilters = [];

    private array $testActions = [];

    private array $testSearch = [];

    /**
     * @param array $testRules
     */
    public function setTestRules(array $testRules): void
    {
        $this->testRules = $testRules;
    }

    /**
     * @param array $testFileds
     */
    public function setTestFields(array $testFileds): void
    {
        $this->testFields = $testFileds;
    }

    /**
     * @param array $testFilters
     */
    public function setTestFilters(array $testFilters): void
    {
        $this->testFilters = $testFilters;
    }

    /**
     * @param array $testActions
     */
    public function setTestActions(array $testActions): void
    {
        $this->testActions = $testActions;
    }

    /**
     * @param array $testSearch
     */
    public function setTestSearch(array $testSearch): void
    {
        $this->testSearch = $testSearch;
    }


    public function rules(Model $item): array
    {
        return $this->testRules;
    }

    public function fields(): array
    {
        return $this->testFields;
    }

    public function filters(): array
    {
        if(empty($this->testFilters)) {
            return ['id'];
        }

        return $this->testFilters;
    }

    public function actions(): array
    {
        if(empty($this->testActions)) {
            return [
                FiltersAction::make(trans('moonshine::ui.filters')),
            ];
        }
        return $this->testActions;
    }

    public function search(): array
    {
        return $this->testSearch;
    }
}
<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Resources;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Resources\ModelResource;

class TestResource extends ModelResource
{
    private array $testRules = [];

    private array $testFields = [];

    private array $testPages = [];

    private array $testValidationMessages = [];

    private array $testFilters = [];

    private array $testSearch = [];

    private array $testActions = [];

    private ?string $testUriKey = null;

    public function pages(): array
    {
        if(! empty($this->testPages)) {
            return $this->testPages;
        }

        return parent::pages();
    }

    public function setTestPages(array $pages)
    {
        $this->testPages = $pages;

        return $this;
    }

    public function setTestPolicy(bool $value): static
    {
        $this->withPolicy = $value;

        return $this;
    }

    public function setTestTitle(string $value): static
    {
        $this->title = $value;

        return $this;
    }

    public function setTestModel(string $model): static
    {
        $this->model = $model;

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

    public function setTestSearch(array $testSearch): static
    {
        $this->testSearch = $testSearch;

        return $this;
    }

    public function setTestUriKey(string $value): static
    {
        $this->testUriKey = $value;

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
        if ($this->testFilters === []) {
            return [];
        }

        return $this->testFilters;
    }

    public function actions(): array
    {
        if ($this->testActions === []) {
            return [
                //FiltersAction::make(trans('moonshine::ui.filters')),
            ];
        }

        return $this->testActions;
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

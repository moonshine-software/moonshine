<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Resources;

use Closure;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Laravel\Resources\ModelResource;

class TestResource extends ModelResource
{
    private array $testRules = [];

    private array $testFields = [];

    private array $testIndexFields = [];

    private array $testFormFields = [];

    private array $testDetailFields = [];

    private array $testExportFields = [];

    private array $testImportFields = [];

    private array $testPages = [];

    private array $testValidationMessages = [];

    private array $testFilters = [];

    private array $testSearch = [];

    private array $testButtons = [];

    private array $testQueryTags = [];

    private array $testMetrics = [];

    private ?Closure $testTdAttributes = null;

    private ?Closure $testTrAttributes = null;

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

    public function setTestIndexFields(array $testFields): static
    {
        $this->testIndexFields = $testFields;

        return $this;
    }

    public function setTestFormFields(array $testFields): static
    {
        $this->testFormFields = $testFields;

        return $this;
    }

    public function setTestDetailFields(array $testFields): static
    {
        $this->testDetailFields = $testFields;

        return $this;
    }

    public function setTestExportFields(array $testFields): static
    {
        $this->testExportFields = $testFields;

        return $this;
    }

    public function setTestImportFields(array $testFields): static
    {
        $this->testImportFields = $testFields;

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

    public function setTestButtons(array $buttons): static
    {
        $this->testButtons = $buttons;

        return $this;
    }

    public function setTestQueryTags(array $queryTags): static
    {
        $this->testQueryTags = $queryTags;

        return $this;
    }

    public function setTestTdAttributes(Closure $fn): static
    {
        $this->testTdAttributes = $fn;

        return $this;
    }

    public function setTestTrAttributes(Closure $fn): static
    {
        $this->testTrAttributes = $fn;

        return $this;
    }

    public function setTestMetrics(array $metrics): static
    {
        $this->testMetrics = $metrics;

        return $this;
    }

    public function rules(Model $item): array
    {
        return $this->testRules;
    }

    public function formFields(): array
    {
        return $this->testFormFields !== []
            ? $this->testFormFields
            : $this->testFields;
    }

    public function indexFields(): array
    {
        return $this->testIndexFields !== []
            ? $this->testIndexFields
            : $this->testFields;
    }

    public function detailFields(): array
    {
        return $this->testDetailFields !== []
            ? $this->testDetailFields
            : $this->testFields;
    }

    public function importFields(): array
    {
        return $this->testImportFields;
    }

    public function exportFields(): array
    {
        return $this->testExportFields;
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

    public function queryTags(): array
    {
        return $this->testQueryTags;
    }

    public function buttons(): array
    {
        return $this->testButtons;
    }

    public function search(): array
    {
        return $this->testSearch;
    }

    public function getUriKey(): string
    {
        if ($this->testUriKey) {
            return $this->testUriKey;
        }

        return parent::getUriKey();
    }

    public function metrics(): array
    {
        return $this->testMetrics;
    }

    public function trAttributes(): Closure
    {
        return $this->testTrAttributes ?? parent::trAttributes();
    }

    public function tdAttributes(): Closure
    {
        return $this->testTdAttributes ?? parent::tdAttributes();
    }

    public function setSimplePaginate(): void
    {
        $this->simplePaginate = true;
    }

    public function setDeleteRelationships(): void
    {
        $this->deleteRelationships = true;
    }
}

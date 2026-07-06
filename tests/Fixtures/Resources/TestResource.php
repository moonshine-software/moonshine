<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Resources;

use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;

class TestResource extends AbstractTestingResource
{
    private array $testRules = [];

    private array $testFields = [];

    private array $testIndexFields = [];

    private array $testFormFields = [];

    private array $testDetailFields = [];

    private array $testExportFields = [];

    private array $testImportFields = [];

    private array $testPages = [];

    private array $testFilters = [];

    private array $testSearch = [];

    private array $testQueryTags = [];

    private ?string $testUriKey = null;

    protected function pages(): array
    {
        if (! empty($this->testPages)) {
            return $this->testPages;
        }

        return parent::pages();
    }

    public function setTestPages(array $pages)
    {
        $this->testPages = $pages;

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

    public function setTestQueryTags(array $queryTags): static
    {
        $this->testQueryTags = $queryTags;

        return $this;
    }

    protected function rules(DataWrapperContract $item): array
    {
        return $this->testRules;
    }

    protected function formFields(): iterable
    {
        return $this->testFormFields !== []
            ? $this->testFormFields
            : $this->testFields;
    }

    protected function indexFields(): iterable
    {
        return $this->testIndexFields !== []
            ? $this->testIndexFields
            : $this->testFields;
    }

    protected function detailFields(): iterable
    {
        return $this->testDetailFields !== []
            ? $this->testDetailFields
            : $this->testFields;
    }

    protected function importFields(): iterable
    {
        return $this->testImportFields;
    }

    protected function exportFields(): iterable
    {
        return $this->testExportFields;
    }

    protected function filters(): iterable
    {
        if ($this->testFilters === []) {
            return [];
        }

        return $this->testFilters;
    }

    protected function queryTags(): array
    {
        return $this->testQueryTags;
    }

    protected function search(): array
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

    public function setSimplePaginate(): void
    {
        $this->simplePaginate = true;
    }

    public function setDeleteRelationships(): void
    {
        $this->deleteRelationships = true;
    }
}

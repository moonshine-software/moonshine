<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Resources;

use MoonShine\Crud\JsonResponse;

class TestCrudResponseResource extends TestResource
{
    public function modalCreation(bool $enabled = true): static
    {
        $this->createInModal = $enabled;

        return $this;
    }

    public function modifySaveResponse(JsonResponse $response): JsonResponse
    {
        return $response->merge(['activePage' => $this->getActivePage()?->getPageType()?->value]);
    }

    public function modifyDestroyResponse(JsonResponse $response): JsonResponse
    {
        return $response->merge(['activePage' => $this->getActivePage()?->getPageType()?->value]);
    }

    public function modifyMassDeleteResponse(JsonResponse $response): JsonResponse
    {
        return $response->merge(['activePage' => $this->getActivePage()?->getPageType()?->value]);
    }
}

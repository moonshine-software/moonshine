<?php

declare(strict_types=1);

namespace MoonShine\Support\DTOs;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/**
 * @implements Arrayable<string, string|null>
 */
final readonly class AsyncCallback implements Arrayable, JsonSerializable
{
    public function __construct(
        /**
         * Called before a request
         */
        private ?string $beforeRequest,

        /**
         * Replaces the default response handler
         */
        private ?string $responseHandler,

        /**
         * Called after standard response processing if $responseHandler is not specified
         */
        private ?string $afterResponse,
    ) {
    }

    public static function with(
        ?string $beforeRequest = null,
        ?string $responseHandler = null,
        ?string $afterResponse = null
    ): self {
        return new self($beforeRequest, $responseHandler, $afterResponse);
    }

    public function getBeforeRequest(): ?string
    {
        return $this->beforeRequest;
    }

    public function getResponseHandler(): ?string
    {
        return $this->responseHandler;
    }

    public function getAfterResponse(): ?string
    {
        return $this->afterResponse;
    }

    /**
     * @return array<string, string|null>
     */
    public function toArray(): array
    {
        return [
            'beforeRequest' => $this->getBeforeRequest(),
            'responseHandler' => $this->getResponseHandler(),
            'afterResponse' => $this->getAfterResponse(),
        ];
    }

    /**
     * @return array<string, string|null>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}

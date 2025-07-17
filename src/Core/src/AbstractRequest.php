<?php

declare(strict_types=1);

namespace MoonShine\Core;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use MoonShine\Contracts\Core\DependencyInjection\RequestContract;
use MoonShine\Core\Traits\InteractsWithRequest;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractRequest implements RequestContract
{
    use InteractsWithRequest;

    public function __construct(
        protected readonly ServerRequestInterface $request,
    ) {
    }

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    abstract public function getSession(string $key, mixed $default = null): mixed;

    abstract public function getFormErrors(?string $bag = null): array;

    abstract public function getOld(string $key, mixed $default = null): mixed;

    abstract public function getFile(string $key): mixed;

    public function get(string $key, mixed $default = null): mixed
    {
        return data_get(
            $this->getAll(),
            $key,
            $default
        );
    }

    public function getScalar(string $key, mixed $default = null): mixed
    {
        $value = $this->get($key, $default);
        $default = \is_scalar($default) ? $default : null;

        return \is_scalar($value) ? $value : $default;
    }

    public function has(string $key): bool
    {
        return $this->get($key, $this) !== $this;
    }

    public function getAll(): Collection
    {
        $body = $this->request->getParsedBody();

        return new Collection(
            array_replace_recursive(
                \is_array($body) ? $body : [],
                $this->request->getUploadedFiles(),
                $this->request->getQueryParams()
            )
        );
    }

    /**
     * @param  string[]|string  $keys
     *
     * @return mixed[]
     */
    public function getOnly(array|string $keys): array
    {
        return $this->getAll()->only($keys)->toArray();
    }

    /**
     * @param  string[]|string  $keys
     *
     * @return mixed[]
     */
    public function getExcept(array|string $keys): array
    {
        return $this->getAll()->except($keys)->toArray();
    }

    public function isAjax(): bool
    {
        return $this->getRequest()->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }

    public function isWantsJson(): bool
    {
        $acceptable = $this->getRequest()->getHeader('Accept');

        return isset($acceptable[0]) && Str::contains(strtolower($acceptable[0]), ['/json', '+json']);
    }
}

<?php

declare(strict_types=1);

use MoonShine\Support\Memoize\Backtrace;
use MoonShine\Support\Memoize\MemoizeRepository;

if (! function_exists('memoize')) {
    /**
     * @template T
     *
     * @param callable(): T $callback
     * @return T
     */
    function memoize(callable $callback): mixed
    {
        $trace = debug_backtrace(
            DEBUG_BACKTRACE_PROVIDE_OBJECT,
            2
        );

        $backtrace = new Backtrace($trace);

        if ($backtrace->getFunctionName() === 'eval') {
            return $callback();
        }

        $object = $backtrace->getObject();

        $hash = $backtrace->getHash();

        $cache = MemoizeRepository::getInstance();

        if (is_string($object)) {
            $object = $cache;
        }

        if (! $cache->isEnabled()) {
            return $callback($backtrace->getArguments());
        }

        if (! $cache->has($object, $hash)) {
            $result = $callback($backtrace->getArguments());

            $cache->set($object, $hash, $result);
        }

        return $cache->get($object, $hash);
    }
}

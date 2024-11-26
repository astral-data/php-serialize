<?php

namespace Astral\Serialize\Support\Caching;

use DateInterval;
use Psr\SimpleCache\CacheInterface;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

class LaravelCache implements CacheInterface
{
    private CacheRepository $repository;

    public function __construct(CacheRepository $repository)
    {
        $this->repository = $repository;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->repository->get($key, $default);
    }

    public function set(string $key, mixed $value, int|DateInterval $ttl = null): bool
    {
        return $this->repository->put($key, $value, $ttl);
    }

    public function delete(string $key): bool
    {
        return $this->repository->forget($key);
    }

    public function clear(): bool
    {
        return $this->repository->clear();
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $results = [];
        foreach ($keys as $key) {
            $results[$key] = $this->get($key, $default);
        }
        return $results;
    }

    public function setMultiple(iterable $values, int|DateInterval $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }
        return true;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
        return true;
    }

    public function has(string $key): bool
    {
        return $this->repository->has($key);
    }
}
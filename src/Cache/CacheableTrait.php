<?php declare(strict_types=1);

namespace STDW\Http\Router\Cache;

trait CacheableTrait
{
    protected const CACHE_KEY = 'routes';

    /** @return bool
     */
    protected function hasRoutesInCache(): bool
    {
        return $this->cache->has(self::CACHE_KEY);
    }

    /**
     * @return array{
     *   'routes': array<int, array<string, array<string, array<string, mixed>>>>,
     *   'names': array<string, string>
     * }
     */
    protected function getRoutesFromCache(): array
    {
        $cached = $this->cache->get(self::CACHE_KEY);

        if ( ! is_array($cached)) {
            $cached = json_decode(json_encode($cached), true) ?: [];
        }

        return [
            'routes' => $cached['routes'] ?? [],
            'names' => $cached['names'] ?? [],
        ];
    }

    /**
     * @param array{
     *   'routes': array<int, array<string, array<string, array<string, mixed>>>>,
     *   'names': array<string, string>
     * } $collection
     * @return void
     */
    protected function setRoutesToCache(array $collection): void
    {
        $this->cache->set(self::CACHE_KEY, $collection);
    }

    /** @return void
     */
    protected function clearRoutesCache(): void
    {
        $this->cache->delete(self::CACHE_KEY);
    }
}

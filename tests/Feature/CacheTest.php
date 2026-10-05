<?php declare(strict_types=1);

use STDW\Cache\Cache;
use STDW\Cache\CacheConfig;
use STDW\Http\Router\RouteCollection;
use STDW\Http\Router\Router;

beforeEach(function () {
    $this->cache = new Cache(new CacheConfig([
        'handler' => 'file',
        'storage' => TMPDIR,
    ]));
    $this->cache->clear();

    $this->collection = new RouteCollection($this->cache);
});

it('saves cache after loading routes', function () {
    $file = __DIR__ . '/../Unit/stubs/routes.php';
    $this->collection->load($file);

    $router = new Router($this->collection, $this->cache, true);

    expect($this->cache->has('routes'))->toBeTrue();
});

it('uses cache when available', function () {
    $cached = [
        'routes' => [0 => ['static' => ['' => ['controller' => 'TestController', 'route' => '/^\/$/']]]],
        'names' => ['home' => ''],
    ];
    $this->cache->set('routes', $cached);

    $router = new Router($this->collection, $this->cache, true);

    expect($this->cache->has('routes'))->toBeTrue();
});

it('deletes cache when withCache is false', function () {
    $this->cache->set('routes', ['routes' => [], 'names' => []]);

    $router = new Router($this->collection, $this->cache, false);

    expect($this->cache->has('routes'))->toBeFalse();
});

it('loads from cache when withCache is true and cache exists', function () {
    $cached = [
        'routes' => [0 => ['static' => ['' => ['controller' => 'TestController', 'route' => '/^\/$/']]]],
        'names' => ['home' => ''],
    ];
    $this->cache->set('routes', $cached);

    $router = new Router($this->collection, $this->cache, true);

    expect($this->cache->has('routes'))->toBeTrue();
});

<?php declare(strict_types=1);

use STDW\Cache\Cache;
use STDW\Cache\CacheConfig;
use STDW\Http\Router\RouteCollection;

beforeEach(function () {
    $this->cache = new Cache(new CacheConfig([
        'handler' => 'file',
        'storage' => TMPDIR,
    ]));
    $this->cache->clear();

    $this->collection = new RouteCollection($this->cache);
});

it('saves and retrieves routes with file handler', function () {
    $file = __DIR__ . '/../../Unit/stubs/routes.php';
    $this->collection->load($file);

    $router = new \STDW\Http\Router\Router($this->collection, $this->cache, true);

    expect($this->cache->has('routes'))->toBeTrue();
});

it('loads routes from file cache', function () {
    $cached = [
        'routes' => [0 => ['static' => ['' => ['controller' => 'TestController', 'route' => '/^\/$/']]]],
        'names' => ['home' => ''],
    ];
    $this->cache->set('routes', $cached);

    $file = __DIR__ . '/../../Unit/stubs/routes.php';
    $this->collection->load($file);

    $all = $this->collection->all();
    expect($all['routes'])->toBe($cached['routes']);
});

it('clears file cache', function () {
    $this->cache->set('routes', ['routes' => [], 'names' => []]);
    $this->cache->clear();

    expect($this->cache->has('routes'))->toBeFalse();
});

it('returns array when associative is true', function () {
    $cache = new Cache(new CacheConfig([
        'handler' => 'file',
        'storage' => TMPDIR,
        'associative' => true,
    ]));

    $collection = new RouteCollection($cache);
    $file = __DIR__ . '/../../Unit/stubs/routes.php';
    $collection->load($file);

    $all = $collection->all();
    expect($all['routes'])->toBeArray();
    expect($all['names'])->toBeArray();
});

it('returns stdClass when associative is false', function () {
    $cache = new Cache(new CacheConfig([
        'handler' => 'file',
        'storage' => TMPDIR,
        'associative' => false,
    ]));

    $collection = new RouteCollection($cache);
    $file = __DIR__ . '/../../Unit/stubs/routes.php';
    $collection->load($file);

    $all = $collection->all();
    expect($all['routes'])->toBeArray();
});

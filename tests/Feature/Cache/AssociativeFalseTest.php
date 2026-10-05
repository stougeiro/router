<?php declare(strict_types=1);

use STDW\Cache\Cache;
use STDW\Cache\CacheConfig;
use STDW\Http\Router\RouteCollection;

beforeEach(function () {
    $this->cache = new Cache(new CacheConfig([
        'handler' => 'file',
        'storage' => TMPDIR,
        'associative' => false,
    ]));
    $this->cache->clear();

    $this->collection = new RouteCollection($this->cache);
});

it('returns stdClass when associative is false', function () {
    $file = __DIR__ . '/../../Unit/stubs/routes.php';
    $this->collection->load($file);

    $all = $this->collection->all();
    expect($all['routes'])->toBeArray();
});

it('generates url with associative false', function () {
    $file = __DIR__ . '/../../Unit/stubs/routes.php';
    $this->collection->load($file);

    $url = $this->collection->generate('users.show', ['id' => 123]);
    expect($url)->toBe('users/123');
});

it('loads routes from cache with associative false', function () {
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

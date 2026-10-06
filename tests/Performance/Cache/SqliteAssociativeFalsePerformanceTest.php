<?php declare(strict_types=1);

use STDW\Cache\Cache;
use STDW\Cache\CacheConfig;
use STDW\Http\Router\RouteCollection;
use STDW\Http\Router\Router;
use STDW\Http\Request;

beforeEach(function () {
    $this->cache = new Cache(new CacheConfig([
        'handler' => 'sqlite',
        'storage' => TMPDIR,
        'associative' => false,
    ]));
    $this->cache->clear();

    $this->collection = new RouteCollection($this->cache);
});

it('loads routes within acceptable time', function () {
    $file = __DIR__ . '/../../Unit/stubs/routes.php';

    $start = microtime(true);
    $this->collection->load($file);
    $duration = microtime(true) - $start;

    expect($duration)->toBeLessThan(0.1);
});

it('matches routes within acceptable time', function () {
    $file = __DIR__ . '/../../Unit/stubs/routes.php';
    $this->collection->load($file);

    $router = new Router($this->collection, $this->cache, true);

    $_SERVER['REQUEST_URI'] = '/users/123';
    $_SERVER['REQUEST_METHOD'] = 'GET';

    $start = microtime(true);
    $route = $router->match(new Request());
    $duration = microtime(true) - $start;

    expect($route)->not->toBeNull();
    expect($duration)->toBeLessThan(0.1);
});

it('generates url within acceptable time', function () {
    $file = __DIR__ . '/../../Unit/stubs/routes.php';
    $this->collection->load($file);

    $start = microtime(true);
    $url = $this->collection->generate('users.show', ['id' => 123]);
    $duration = microtime(true) - $start;

    expect($url)->toBe('users/123');
    expect($duration)->toBeLessThan(0.1);
});

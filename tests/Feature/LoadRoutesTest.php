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

it('loads routes from file and populates collection', function () {
    $file = __DIR__ . '/../Unit/stubs/routes.php';
    $this->collection->load($file);

    $all = $this->collection->all();

    expect($all['routes'])->toBeArray()
        ->and($all['names'])->toBeArray()
        ->and($all['names'])->toHaveKeys(['users.show', 'posts.show']);
});

it('loads routes from cache when available', function () {
    $cached = [
        'routes' => [0 => ['static' => ['' => ['controller' => 'TestController', 'route' => '/^\/$/']]]],
        'names' => ['home' => ''],
    ];
    $this->cache->set('routes', $cached);

    $file = __DIR__ . '/../Unit/stubs/routes.php';
    $this->collection->load($file);

    $all = $this->collection->all();

    expect($all['routes'])->toBe($cached['routes'])
        ->and($all['names'])->toBe($cached['names']);
});

it('loads nested routes correctly', function () {
    $file = __DIR__ . '/../Unit/stubs/routes.php';
    $this->collection->load($file);

    $all = $this->collection->all();

    expect($all['routes'][2]['dynamic']['users/{id:num}'])->toBeArray()
        ->and($all['routes'][2]['dynamic']['users/{id:num}']['controller'])->toBe(App\Controllers\UserController::class);
});

it('loads named routes correctly', function () {
    $file = __DIR__ . '/../Unit/stubs/routes.php';
    $this->collection->load($file);

    $all = $this->collection->all();

    expect($all['names']['users.show'])->toBe('users/{id:num}')
        ->and($all['names']['posts.show'])->toBe('posts/{slug:slug}');
});

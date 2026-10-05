<?php declare(strict_types=1);

use STDW\Cache\Cache;
use STDW\Cache\CacheConfig;
use STDW\Http\Request;
use STDW\Http\Uri;
use STDW\Http\Router\Route;
use STDW\Http\Router\Router;

beforeEach(function () {
    $this->cache = new Cache(new CacheConfig([
        'handler' => 'file',
        'storage' => TMPDIR,
    ]));
    $this->cache->clear();

    $this->collection = new \STDW\Http\Router\RouteCollection($this->cache);
    $file = __DIR__ . '/../Unit/stubs/routes.php';
    $this->collection->load($file);

    $this->router = new Router($this->collection, $this->cache, true);
});

it('matches static route', function () {
    $_SERVER['REQUEST_URI'] = '/users';
    $_SERVER['REQUEST_METHOD'] = 'GET';

    $request = new Request();
    $route = $this->router->match($request);

    expect($route)->toBeInstanceOf(Route::class)
        ->and($route->getUri())->toBe('users')
        ->and($route->getController())->toBe(App\Controllers\UserController::class);
});

it('matches dynamic route and extracts variables', function () {
    $_SERVER['REQUEST_URI'] = '/users/123';
    $_SERVER['REQUEST_METHOD'] = 'GET';

    $request = new Request();
    $route = $this->router->match($request);

    expect($route)->toBeInstanceOf(Route::class)
        ->and($route->getUri())->toBe('users/123')
        ->and($route->getMap())->toBe('users/{id:num}')
        ->and($route->getVariables())->toBe(['id' => '123']);
});

it('returns null when no route matches', function () {
    $_SERVER['REQUEST_URI'] = '/nonexistent';
    $_SERVER['REQUEST_METHOD'] = 'GET';

    $request = new Request();
    $route = $this->router->match($request);

    expect($route)->toBeNull();
});

it('matches route with slug placeholder', function () {
    $_SERVER['REQUEST_URI'] = '/posts/hello-world';
    $_SERVER['REQUEST_METHOD'] = 'GET';

    $request = new Request();
    $route = $this->router->match($request);

    expect($route)->toBeInstanceOf(Route::class)
        ->and($route->getUri())->toBe('posts/hello-world')
        ->and($route->getMap())->toBe('posts/{slug:slug}')
        ->and($route->getVariables())->toBe(['slug' => 'hello-world']);
});

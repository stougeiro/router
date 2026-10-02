<?php declare(strict_types=1);

use STDW\Contract\Cache\CacheInterface;
use STDW\Http\Router\Exception\RouterException;
use Tests\Support\TestableRouteCollection;

beforeEach(function () {
    $this->cache = new class implements CacheInterface {
        private array $storage = [];

        public function has(string $key): bool
        {
            return isset($this->storage[$key]);
        }

        public function get(string $key, mixed $default = null): mixed
        {
            return $this->storage[$key] ?? $default;
        }

        public function set(string $key, mixed $value, int $ttl = 300): bool
        {
            $this->storage[$key] = $value;
            return true;
        }

        public function delete(string $key): bool
        {
            unset($this->storage[$key]);
            return true;
        }

        public function clear(): bool
        {
            $this->storage = [];
            return true;
        }
    };

    $this->collection = new TestableRouteCollection($this->cache);
});

it('loads routes from file', function () {
    $file = __DIR__ . '/stubs/routes.php';
    $this->collection->load($file);

    $all = $this->collection->all();

    expect($all['routes'])->toBeArray()
        ->and($all['names'])->toBeArray();
});

it('loads routes from cache when available', function () {
    $cached = [
        'routes' => [0 => ['static' => ['' => ['controller' => 'TestController', 'route' => '/^\/$/']]]],
        'names' => ['home' => ''],
    ];
    $this->cache->set('routes', $cached);

    $file = __DIR__ . '/stubs/routes.php';
    $this->collection->load($file);

    $all = $this->collection->all();

    expect($all['routes'])->toBe($cached['routes'])
        ->and($all['names'])->toBe($cached['names']);
});

it('throws when file not found', function () {
    $this->collection->load('/nonexistent/file.php');
})->throws(RouterException::class, "Routes file '/nonexistent/file.php' not found");

it('throws when file returns non-array', function () {
    $file = __DIR__ . '/stubs/invalid-routes.php';
    $this->collection->load($file);
})->throws(RouterException::class, "must return an array");

it('generates url from named route', function () {
    $file = __DIR__ . '/stubs/routes.php';
    $this->collection->load($file);

    $url = $this->collection->generate('users.show', ['id' => 123]);

    expect($url)->toBe('users/123');
});

it('throws when generating url for non-existent named route', function () {
    $this->collection->generate('nonexistent');
})->throws(RouterException::class, "Named route 'nonexistent' not found");

it('throws when generating url with missing variable', function () {
    $file = __DIR__ . '/stubs/routes.php';
    $this->collection->load($file);

    $this->collection->generate('users.show', []);
})->throws(RouterException::class, "Variable 'id' is required for route");

it('returns all routes and names', function () {
    $file = __DIR__ . '/stubs/routes.php';
    $this->collection->load($file);

    $all = $this->collection->all();

    expect($all)->toHaveKeys(['routes', 'names']);
});

it('maps route with fqcn', function () {
    $this->collection->callMap(['users' => 'App\Controllers\UserController']);

    $all = $this->collection->all();

    expect($all['routes'][1]['static']['users']['controller'])->toBe('App\Controllers\UserController');
});

it('maps route with name', function () {
    $this->collection->callMap(['users' => ['App\Controllers\UserController' => 'users.index']]);

    $all = $this->collection->all();

    expect($all['names'])->toHaveKey('users.index')
        ->and($all['names']['users.index'])->toBe('users');
});

it('maps nested routes', function () {
    $this->collection->callMap([
        'api' => [
            'users' => 'App\Api\Controllers\UserController',
        ],
    ]);

    $all = $this->collection->all();

    expect($all['routes'][2]['static']['api/users']['controller'])->toBe('App\Api\Controllers\UserController');
});

it('throws on invalid route mapping', function () {
    $this->collection->callMap(['users' => 123]);
})->throws(RouterException::class, "cannot be mapped");

it('adds static route', function () {
    $this->collection->callAdd('App\Controllers\UserController', [
        'map' => 'users',
        'segments' => 1,
        'route' => '/^users$/',
        'type' => 'static',
    ]);

    $all = $this->collection->all();

    expect($all['routes'][1]['static']['users']['controller'])->toBe('App\Controllers\UserController');
});

it('adds dynamic route', function () {
    $this->collection->callAdd('App\Controllers\UserController', [
        'map' => 'users/{id:num}',
        'segments' => 2,
        'route' => '/^users\/(?P<id>[0-9]+)$/',
        'type' => 'dynamic',
    ]);

    $all = $this->collection->all();

    expect($all['routes'][2]['dynamic']['users/{id:num}']['controller'])->toBe('App\Controllers\UserController');
});

it('nominates route with valid name', function () {
    $this->collection->callAdd('App\Controllers\UserController', [
        'map' => 'users',
        'segments' => 1,
        'route' => '/^users$/',
        'type' => 'static',
    ]);

    $this->collection->callNominate('users.index', [
        'map' => 'users',
        'segments' => 1,
        'route' => '/^users$/',
        'type' => 'static',
    ]);

    $all = $this->collection->all();

    expect($all['names'])->toHaveKey('users.index')
        ->and($all['names']['users.index'])->toBe('users');
});

it('throws on invalid route name', function () {
    $this->collection->callNominate('INVALID', [
        'map' => 'users',
        'segments' => 1,
        'route' => '/^users$/',
        'type' => 'static',
    ]);
})->throws(RouterException::class, "Invalid route name 'INVALID'. Route names must match: [a-z0-9.]");

it('throws on duplicate route name', function () {
    $this->collection->callAdd('App\Controllers\UserController', [
        'map' => 'users',
        'segments' => 1,
        'route' => '/^users$/',
        'type' => 'static',
    ]);

    $this->collection->callNominate('users.index', [
        'map' => 'users',
        'segments' => 1,
        'route' => '/^users$/',
        'type' => 'static',
    ]);

    $this->collection->callNominate('users.index', [
        'map' => 'users',
        'segments' => 1,
        'route' => '/^users$/',
        'type' => 'static',
    ]);
})->throws(RouterException::class, "Route 'users' cannot be named by already registered 'users.index'");

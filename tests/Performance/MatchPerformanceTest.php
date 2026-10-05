<?php declare(strict_types=1);

use STDW\Cache\Cache;
use STDW\Cache\CacheConfig;
use STDW\Http\Request;
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

it('matches static route within acceptable time', function () {
    $_SERVER['REQUEST_URI'] = '/users';
    $_SERVER['REQUEST_METHOD'] = 'GET';

    $start = microtime(true);
    $route = $this->router->match(new Request());
    $duration = microtime(true) - $start;

    expect($route)->not->toBeNull()
        ->and($duration)->toBeLessThan(0.1);
});

it('matches dynamic route within acceptable time', function () {
    $_SERVER['REQUEST_URI'] = '/users/123';
    $_SERVER['REQUEST_METHOD'] = 'GET';

    $start = microtime(true);
    $route = $this->router->match(new Request());
    $duration = microtime(true) - $start;

    expect($route)->not->toBeNull()
        ->and($duration)->toBeLessThan(0.1);
});

it('matches route with many dynamic routes within acceptable time', function () {
    $this->cache->delete('routes');

    $routes = [];
    for ($i = 0; $i < 100; $i++) {
        $routes["/route{$i}/{id:num}"] = App\Controllers\StubController::class;
    }

    $file = __DIR__ . '/../Unit/stubs/many-dynamic-routes.php';
    file_put_contents($file, '<?php return ' . var_export($routes, true) . ';');

    $collection = new \STDW\Http\Router\RouteCollection($this->cache);
    $collection->load($file);

    $router = new Router($collection, $this->cache, false);

    $_SERVER['REQUEST_URI'] = '/route50/123';
    $_SERVER['REQUEST_METHOD'] = 'GET';

    $start = microtime(true);
    $route = $router->match(new Request());
    $duration = microtime(true) - $start;

    expect($route)->not->toBeNull()
        ->and($duration)->toBeLessThan(1.0);

    unlink($file);
});

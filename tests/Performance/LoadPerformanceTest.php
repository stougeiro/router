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

it('loads routes from file within acceptable time', function () {
    $file = __DIR__ . '/../Unit/stubs/routes.php';

    $start = microtime(true);
    $this->collection->load($file);
    $duration = microtime(true) - $start;

    expect($duration)->toBeLessThan(0.1);
});

it('loads routes from cache within acceptable time', function () {
    $cached = [
        'routes' => [0 => ['static' => ['' => ['controller' => 'TestController', 'route' => '/^\/$/']]]],
        'names' => ['home' => ''],
    ];
    $this->cache->set('routes', $cached);

    $file = __DIR__ . '/../Unit/stubs/routes.php';

    $start = microtime(true);
    $this->collection->load($file);
    $duration = microtime(true) - $start;

    expect($duration)->toBeLessThan(0.1);
});

it('loads many routes within acceptable time', function () {
    $routes = [];
    for ($i = 0; $i < 1000; $i++) {
        $routes["route{$i}"] = App\Controllers\StubController::class;
    }

    $file = __DIR__ . '/../Unit/stubs/many-routes.php';
    file_put_contents($file, '<?php return ' . var_export($routes, true) . ';');

    $start = microtime(true);
    $this->collection->load($file);
    $duration = microtime(true) - $start;

    expect($duration)->toBeLessThan(1.0);

    unlink($file);
});

<?php declare(strict_types=1);

use STDW\Contract\Cache\CacheInterface;
use STDW\Http\Router\RouteCollection;

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

    $this->collection = new RouteCollection($this->cache);
});

it('loads routes from file within acceptable time', function () {
    $file = __DIR__ . '/../Unit/stubs/routes.php';

    $start = microtime(true);
    $this->collection->load($file);
    $duration = microtime(true) - $start;

    expect($duration)->toBeLessThan(1.0);
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

    expect($duration)->toBeLessThan(0.01);
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

    expect($duration)->toBeLessThan(5.0);

    unlink($file);
});

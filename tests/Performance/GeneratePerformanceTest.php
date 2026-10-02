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
    $file = __DIR__ . '/../Unit/stubs/routes.php';
    $this->collection->load($file);
});

it('generates url within acceptable time', function () {
    $start = microtime(true);
    $url = $this->collection->generate('users.show', ['id' => 123]);
    $duration = microtime(true) - $start;

    expect($url)->toBe('users/123')
        ->and($duration)->toBeLessThan(0.1);
});

it('generates url with many variables within acceptable time', function () {
    $routes = [];
    for ($i = 0; $i < 100; $i++) {
        $routes["route{$i}/{id:num}/post/{slug:slug}"] = [
            App\Controllers\StubController::class => "route{$i}.show",
        ];
    }

    $file = __DIR__ . '/../Unit/stubs/many-vars-routes.php';
    file_put_contents($file, '<?php return ' . var_export($routes, true) . ';');

    $collection = new RouteCollection($this->cache);
    $collection->load($file);

    $start = microtime(true);
    $url = $collection->generate('route50.show', ['id' => 123, 'slug' => 'hello']);
    $duration = microtime(true) - $start;

    expect($url)->toBe('route50/123/post/hello')
        ->and($duration)->toBeLessThan(0.1);

    unlink($file);
});

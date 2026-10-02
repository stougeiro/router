<?php declare(strict_types=1);

use STDW\Contract\Cache\CacheInterface;
use STDW\Contract\Http\RequestInterface;
use STDW\Contract\Http\UriInterface;
use STDW\Http\Router\Router;

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

    $this->uri = new class implements UriInterface {
        private string $path;

        public function setPath(string $path): void
        {
            $this->path = $path;
        }

        public static function fromUrl(string $url): UriInterface
        {
            $uri = new self();
            $uri->setPath(parse_url($url, PHP_URL_PATH) ?? '/');
            return $uri;
        }

        public static function fromArray(array $data): UriInterface
        {
            $uri = new self();
            $uri->setPath($data['path'] ?? '/');
            return $uri;
        }

        public function getScheme(): string { return 'http'; }
        public function getHost(): string { return 'localhost'; }
        public function getPort(): ?int { return null; }
        public function getUser(): ?string { return null; }
        public function getPass(): ?string { return null; }
        public function getAuthority(): string { return 'localhost'; }
        public function getPath(): string { return $this->path; }
        public function getQuery(): array { return []; }
        public function getFragment(): ?string { return null; }
        public function __toString(): string { return $this->path; }
    };

    $this->request = new class($this->uri) implements RequestInterface {
        private UriInterface $uri;

        public function __construct(UriInterface $uri)
        {
            $this->uri = $uri;
        }

        public function getMethod(): string { return 'GET'; }
        public function getUri(): UriInterface { return $this->uri; }
        public function getHeaders(): array { return []; }
        public function getHeader(string $name): ?string { return null; }
        public function hasHeader(string $name): bool { return false; }
        public function getCookies(): array { return []; }
        public function getCookie(string $name): ?array { return null; }
        public function getParams(): array { return []; }
        public function param(string $key, mixed $default = null): mixed { return $default; }
        public function getBody(): mixed { return null; }
        public function input(string $key, mixed $default = null): mixed { return $default; }
        public function getUploadedFiles(): array { return []; }
        public function getUploadedFile(string $name): null|array|\STDW\Contract\Http\UploadedFileInterface { return null; }
        public function getAttributes(): array { return []; }
        public function getAttribute(string $name): mixed { return null; }
        public function withAttribute(string $name, mixed $value): RequestInterface { return $this; }
        public function withoutAttribute(string $name): RequestInterface { return $this; }
    };

    $this->collection = new \STDW\Http\Router\RouteCollection($this->cache);
    $file = __DIR__ . '/../Unit/stubs/routes.php';
    $this->collection->load($file);

    $this->router = new Router($this->collection, $this->cache, true);
});

it('matches static route within acceptable time', function () {
    $this->uri->setPath('/users');

    $start = microtime(true);
    $route = $this->router->match($this->request);
    $duration = microtime(true) - $start;

    expect($route)->not->toBeNull()
        ->and($duration)->toBeLessThan(0.1);
});

it('matches dynamic route within acceptable time', function () {
    $this->uri->setPath('/users/123');

    $start = microtime(true);
    $route = $this->router->match($this->request);
    $duration = microtime(true) - $start;

    expect($route)->not->toBeNull()
        ->and($duration)->toBeLessThan(0.1);
});

it('matches route with many dynamic routes within acceptable time', function () {
    $routes = [];
    for ($i = 0; $i < 100; $i++) {
        $routes["route{$i}/{id:num}"] = App\Controllers\StubController::class;
    }

    $file = __DIR__ . '/../Unit/stubs/many-dynamic-routes.php';
    file_put_contents($file, '<?php return ' . var_export($routes, true) . ';');

    $collection = new \STDW\Http\Router\RouteCollection($this->cache);
    $collection->load($file);

    $router = new Router($collection, $this->cache, true);

    $this->uri->setPath('/route50/123');

    $start = microtime(true);
    $route = $router->match($this->request);
    $duration = microtime(true) - $start;

    expect($route)->not->toBeNull()
        ->and($duration)->toBeLessThan(1.0);

    unlink($file);
});

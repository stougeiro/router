<?php declare(strict_types=1);

use STDW\Contract\Cache\CacheInterface;
use STDW\Contract\Http\RequestInterface;
use STDW\Contract\Http\UriInterface;
use STDW\Http\Router\Route;
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

it('matches static route', function () {
    $this->uri->setPath('/users');
    $route = $this->router->match($this->request);

    expect($route)->toBeInstanceOf(Route::class)
        ->and($route->getUri())->toBe('users')
        ->and($route->getController())->toBe(App\Controllers\UserController::class);
});

it('matches dynamic route and extracts variables', function () {
    $this->uri->setPath('/users/123');
    $route = $this->router->match($this->request);

    expect($route)->toBeInstanceOf(Route::class)
        ->and($route->getUri())->toBe('users/123')
        ->and($route->getMap())->toBe('users/{id:num}')
        ->and($route->getVariables())->toBe(['id' => '123']);
});

it('returns null when no route matches', function () {
    $this->uri->setPath('/nonexistent');
    $route = $this->router->match($this->request);

    expect($route)->toBeNull();
});

it('matches route with slug placeholder', function () {
    $this->uri->setPath('/posts/hello-world');
    $route = $this->router->match($this->request);

    expect($route)->toBeInstanceOf(Route::class)
        ->and($route->getUri())->toBe('posts/hello-world')
        ->and($route->getMap())->toBe('posts/{slug:slug}')
        ->and($route->getVariables())->toBe(['slug' => 'hello-world']);
});

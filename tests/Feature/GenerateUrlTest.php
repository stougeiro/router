<?php declare(strict_types=1);

use STDW\Cache\Cache;
use STDW\Cache\CacheConfig;
use STDW\Http\Router\RouteCollection;

beforeEach(function () {
    $this->cache = new Cache(new CacheConfig([
        'handler' => 'file',
        'storage' => sys_get_temp_dir() . '/router-cache-test',
    ]));
    $this->cache->clear();

    $this->collection = new RouteCollection($this->cache);
    $file = __DIR__ . '/../Unit/stubs/routes.php';
    $this->collection->load($file);
});

it('generates url for named route with single variable', function () {
    $url = $this->collection->generate('users.show', ['id' => 123]);

    expect($url)->toBe('users/123');
});

it('generates url for named route with slug variable', function () {
    $url = $this->collection->generate('posts.show', ['slug' => 'hello-world']);

    expect($url)->toBe('posts/hello-world');
});

it('generates url with numeric string variable', function () {
    $url = $this->collection->generate('users.show', ['id' => '456']);

    expect($url)->toBe('users/456');
});

it('throws when generating url for non-existent named route', function () {
    $this->collection->generate('nonexistent');
})->throws(\STDW\Http\Router\Exception\RouterException::class, "Named route 'nonexistent' not found");

it('throws when generating url with missing variable', function () {
    $this->collection->generate('users.show', []);
})->throws(\STDW\Http\Router\Exception\RouterException::class, "Variable 'id' is required for route 'users.show: users/{id:num}'");

<?php declare(strict_types=1);

use STDW\Cache\Cache;
use STDW\Cache\CacheConfig;
use STDW\Http\Router\RouteCollection;
use STDW\Http\Router\Router;
use STDW\Http\Request;

function measureLoad(string $handler, bool $associative): float
{
    $cache = new Cache(new CacheConfig([
        'handler' => $handler,
        'storage' => TMPDIR,
        'associative' => $associative,
    ]));
    $cache->clear();

    $collection = new RouteCollection($cache);
    $file = __DIR__ . '/../../Unit/stubs/routes.php';

    $start = microtime(true);
    $collection->load($file);
    $duration = microtime(true) - $start;

    return $duration;
}

function measureMatch(string $handler, bool $associative): float
{
    $cache = new Cache(new CacheConfig([
        'handler' => $handler,
        'storage' => TMPDIR,
        'associative' => $associative,
    ]));
    $cache->clear();

    $collection = new RouteCollection($cache);
    $file = __DIR__ . '/../../Unit/stubs/routes.php';
    $collection->load($file);

    $router = new Router($collection, $cache, true);

    $_SERVER['REQUEST_URI'] = '/users/123';
    $_SERVER['REQUEST_METHOD'] = 'GET';

    $start = microtime(true);
    $route = $router->match(new Request());
    $duration = microtime(true) - $start;

    return $duration;
}

function measureGenerate(string $handler, bool $associative): float
{
    $cache = new Cache(new CacheConfig([
        'handler' => $handler,
        'storage' => TMPDIR,
        'associative' => $associative,
    ]));
    $cache->clear();

    $collection = new RouteCollection($cache);
    $file = __DIR__ . '/../../Unit/stubs/routes.php';
    $collection->load($file);

    $start = microtime(true);
    $url = $collection->generate('users.show', ['id' => 123]);
    $duration = microtime(true) - $start;

    return $duration;
}

it('compares file associative true vs false', function () {
    $fileTrue = measureLoad('file', true);
    $fileFalse = measureLoad('file', false);

    fwrite(STDERR, "File associative true: " . number_format($fileTrue, 6) . "s");
    fwrite(STDERR, "File associative false: " . number_format($fileFalse, 6) . "s");

    expect($fileTrue)->toBeLessThan(0.1);
    expect($fileFalse)->toBeLessThan(0.1);
});

it('compares sqlite associative true vs false', function () {
    $sqliteTrue = measureLoad('sqlite', true);
    $sqliteFalse = measureLoad('sqlite', false);

    fwrite(STDERR, "Sqlite associative true: " . number_format($sqliteTrue, 6) . "s");
    fwrite(STDERR, "Sqlite associative false: " . number_format($sqliteFalse, 6) . "s");

    expect($sqliteTrue)->toBeLessThan(0.1);
    expect($sqliteFalse)->toBeLessThan(0.1);
});

it('compares file vs sqlite with associative true', function () {
    $fileTrue = measureLoad('file', true);
    $sqliteTrue = measureLoad('sqlite', true);

    fwrite(STDERR, "File associative true: " . number_format($fileTrue, 6) . "s");
    fwrite(STDERR, "Sqlite associative true: " . number_format($sqliteTrue, 6) . "s");

    expect($fileTrue)->toBeLessThan(0.1);
    expect($sqliteTrue)->toBeLessThan(0.1);
});

it('compares file vs sqlite with associative false', function () {
    $fileFalse = measureLoad('file', false);
    $sqliteFalse = measureLoad('sqlite', false);

    fwrite(STDERR, "File associative false: " . number_format($fileFalse, 6) . "s");
    fwrite(STDERR, "Sqlite associative false: " . number_format($sqliteFalse, 6) . "s");

    expect($fileFalse)->toBeLessThan(0.1);
    expect($sqliteFalse)->toBeLessThan(0.1);
});

it('compares match performance across all modes', function () {
    $fileTrueMatch = measureMatch('file', true);
    $fileFalseMatch = measureMatch('file', false);
    $sqliteTrueMatch = measureMatch('sqlite', true);
    $sqliteFalseMatch = measureMatch('sqlite', false);

    fwrite(STDERR, "Match - File associative true: " . number_format($fileTrueMatch, 6) . "s");
    fwrite(STDERR, "Match - File associative false: " . number_format($fileFalseMatch, 6) . "s");
    fwrite(STDERR, "Match - Sqlite associative true: " . number_format($sqliteTrueMatch, 6) . "s");
    fwrite(STDERR, "Match - Sqlite associative false: " . number_format($sqliteFalseMatch, 6) . "s");

    expect($fileTrueMatch)->toBeLessThan(0.1);
    expect($fileFalseMatch)->toBeLessThan(0.1);
    expect($sqliteTrueMatch)->toBeLessThan(0.1);
    expect($sqliteFalseMatch)->toBeLessThan(0.1);
});

it('compares generate performance across all modes', function () {
    $fileTrueGen = measureGenerate('file', true);
    $fileFalseGen = measureGenerate('file', false);
    $sqliteTrueGen = measureGenerate('sqlite', true);
    $sqliteFalseGen = measureGenerate('sqlite', false);

    fwrite(STDERR, "Generate - File associative true: " . number_format($fileTrueGen, 6) . "s");
    fwrite(STDERR, "Generate - File associative false: " . number_format($fileFalseGen, 6) . "s");
    fwrite(STDERR, "Generate - Sqlite associative true: " . number_format($sqliteTrueGen, 6) . "s");
    fwrite(STDERR, "Generate - Sqlite associative false: " . number_format($sqliteFalseGen, 6) . "s");

    expect($fileTrueGen)->toBeLessThan(0.1);
    expect($fileFalseGen)->toBeLessThan(0.1);
    expect($sqliteTrueGen)->toBeLessThan(0.1);
    expect($sqliteFalseGen)->toBeLessThan(0.1);
});

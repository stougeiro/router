<?php declare(strict_types=1);

use STDW\Http\Router\Exception\RouterException;

it('creates unknown placeholder exception', function () {
    $exception = RouterException::unknownPlaceholder('unknown', ['num', 'slug']);

    expect($exception)->toBeInstanceOf(RouterException::class)
        ->and($exception->getMessage())->toContain("Unknown placeholder 'unknown'")
        ->and($exception->getMessage())->toContain('num')
        ->and($exception->getMessage())->toContain('slug');
});

it('creates invalid placeholder name exception', function () {
    $exception = RouterException::invalidPlaceholderName('INVALID');

    expect($exception)->toBeInstanceOf(RouterException::class)
        ->and($exception->getMessage())->toContain("Invalid placeholder name 'INVALID'");
});

it('creates file not found exception', function () {
    $exception = RouterException::fileNotFound('/path/to/file.php');

    expect($exception)->toBeInstanceOf(RouterException::class)
        ->and($exception->getMessage())->toContain("Routes file '/path/to/file.php' not found");
});

it('creates file content not valid exception', function () {
    $exception = RouterException::fileContentNotValid('/path/to/file.php');

    expect($exception)->toBeInstanceOf(RouterException::class)
        ->and($exception->getMessage())->toContain("Routes file '/path/to/file.php' must return an array");
});

it('creates route not mapped exception', function () {
    $exception = RouterException::routeNotMapped('users');

    expect($exception)->toBeInstanceOf(RouterException::class)
        ->and($exception->getMessage())->toContain("Route 'users' cannot be mapped");
});

it('creates invalid route name exception', function () {
    $exception = RouterException::invalidRouteName('INVALID');

    expect($exception)->toBeInstanceOf(RouterException::class)
        ->and($exception->getMessage())->toContain("Invalid route name 'INVALID'");
});

it('creates route name already registered exception', function () {
    $exception = RouterException::routeNameAlreadyRegistered('users', 'users.index');

    expect($exception)->toBeInstanceOf(RouterException::class)
        ->and($exception->getMessage())->toContain("Route 'users' cannot be named by already registered 'users.index'");
});

it('creates named route not found exception', function () {
    $exception = RouterException::namedRouteNotFound('users.index');

    expect($exception)->toBeInstanceOf(RouterException::class)
        ->and($exception->getMessage())->toContain("Named route 'users.index' not found");
});

it('creates missing variable exception', function () {
    $exception = RouterException::missingVariable('id', 'users.show');

    expect($exception)->toBeInstanceOf(RouterException::class)
        ->and($exception->getMessage())->toContain("Variable 'id' is required for route 'users.show'");
});

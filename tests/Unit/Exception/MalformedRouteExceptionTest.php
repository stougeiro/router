<?php declare(strict_types=1);

use STDW\Http\Router\Exception\MalformedRouteException;

it('creates duplicate slashes exception', function () {
    $exception = MalformedRouteException::duplicateSlashes('users//posts');

    expect($exception)->toBeInstanceOf(MalformedRouteException::class)
        ->and($exception->getMessage())->toContain("URI contains duplicate slashes: 'users//posts'");
});

it('creates invalid characters exception', function () {
    $exception = MalformedRouteException::invalidCharacters('users@posts');

    expect($exception)->toBeInstanceOf(MalformedRouteException::class)
        ->and($exception->getMessage())->toContain("Route contains invalid characters: 'users@posts'");
});

it('creates invalid characters exception with whitespace', function () {
    $exception = MalformedRouteException::invalidCharacters('users posts');

    expect($exception)->toBeInstanceOf(MalformedRouteException::class)
        ->and($exception->getMessage())->toContain("Route contains invalid characters: 'users posts'");
});

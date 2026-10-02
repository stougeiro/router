<?php declare(strict_types=1);

use STDW\Http\Router\Exception\MalformedRouteException;
use Tests\Support\TestableRouteParser;

beforeEach(function () {
    $this->parser = new TestableRouteParser();
});

it('parses static route', function () {
    $result = $this->parser->parse('users');

    expect($result['map'])->toBe('users')
        ->and($result['segments'])->toBe(1)
        ->and($result['variables'])->toBeFalse()
        ->and($result['route'])->toBe('/^users$/');
});

it('parses dynamic route with placeholder', function () {
    $result = $this->parser->parse('users/{id:num}');

    expect($result['map'])->toBe('users/{id:num}')
        ->and($result['segments'])->toBe(2)
        ->and($result['variables'])->toBeTrue()
        ->and($result['route'])->toBe('/^users\/(?P<id>[0-9]+)$/');
});

it('parses route with multiple placeholders', function () {
    $result = $this->parser->parse('users/{id:num}/posts/{slug:slug}');

    expect($result['map'])->toBe('users/{id:num}/posts/{slug:slug}')
        ->and($result['segments'])->toBe(4)
        ->and($result['variables'])->toBeTrue()
        ->and($result['route'])->toBe('/^users\/(?P<id>[0-9]+)\/posts\/(?P<slug>[a-z0-9-]+)$/');
});

it('parses route with dot', function () {
    $result = $this->parser->parse('users.json');

    expect($result['map'])->toBe('users.json')
        ->and($result['segments'])->toBe(1)
        ->and($result['variables'])->toBeFalse()
        ->and($result['route'])->toBe('/^users.json$/');
});

it('validates and removes trailing slash', function () {
    $result = $this->parser->callValidate('users/');

    expect($result)->toBe('users');
});

it('validates and removes leading slash', function () {
    $result = $this->parser->callValidate('/users');

    expect($result)->toBe('users');
});

it('validates and removes both leading and trailing slashes', function () {
    $result = $this->parser->callValidate('/users/');

    expect($result)->toBe('users');
});

it('throws on duplicate slashes', function () {
    $this->parser->callValidate('users//posts');
})->throws(MalformedRouteException::class, "URI contains duplicate slashes: 'users//posts'");

it('throws on invalid characters', function () {
    $this->parser->callValidate('users@posts');
})->throws(MalformedRouteException::class, "Route contains invalid characters: 'users@posts'");

it('throws on whitespace', function () {
    $this->parser->callValidate('users posts');
})->throws(MalformedRouteException::class, "Route contains invalid characters: 'users posts'");

it('throws on special characters', function () {
    $this->parser->callValidate('users!posts');
})->throws(MalformedRouteException::class, "Route contains invalid characters: 'users!posts'");

<?php declare(strict_types=1);

use STDW\Http\Router\Exception\RouterException;
use Tests\Support\TestablePlaceholderRegistry;

beforeEach(function () {
    $this->registry = new TestablePlaceholderRegistry();
});

it('constructs with default placeholders', function () {
    $defaults = $this->registry->callDefaults();

    expect($defaults)->toBeArray()
        ->and($defaults)->toHaveKeys(['any', 'num', 'word', 'slug', 'color', 'year', 'month', 'day', 'token']);
});

it('constructs with custom placeholders merged with defaults', function () {
    $registry = new TestablePlaceholderRegistry(['custom' => '[a-z]+']);

    $all = $registry->all();

    expect($all)->toHaveKey('custom')
        ->and($all)->toHaveKey('num')
        ->and($all['custom'])->toBe('[a-z]+');
});

it('registers valid placeholder', function () {
    $this->registry->register('custom', '[a-z]+');

    expect($this->registry->replace('custom'))->toBe('[a-z]+');
});

it('throws on invalid placeholder name', function () {
    $this->registry->register('INVALID', '[a-z]+');
})->throws(RouterException::class, "Invalid placeholder name 'INVALID'. Placeholder names must match: [a-z0-9.]");

it('throws on placeholder name with special characters', function () {
    $this->registry->register('invalid@name', '[a-z]+');
})->throws(RouterException::class, "Invalid placeholder name 'invalid@name'. Placeholder names must match: [a-z0-9.]");

it('replaces known placeholder with regex', function () {
    expect($this->registry->replace('num'))->toBe('[0-9]+')
        ->and($this->registry->replace('slug'))->toBe('[a-z0-9-]+')
        ->and($this->registry->replace('any'))->toBe('[^/]+');
});

it('throws on unknown placeholder', function () {
    $this->registry->replace('unknown');
})->throws(RouterException::class, "Unknown placeholder 'unknown'. Registered placeholders:");

it('returns all placeholders', function () {
    $all = $this->registry->all();

    expect($all)->toBeArray()
        ->and($all)->toHaveKey('num')
        ->and($all)->toHaveKey('slug');
});

it('overrides default placeholder with custom', function () {
    $registry = new TestablePlaceholderRegistry(['num' => '[0-9]{5}']);

    expect($registry->replace('num'))->toBe('[0-9]{5}');
});

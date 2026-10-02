<?php declare(strict_types=1);

use function STDW\Http\Router\Helper\count_segments;

it('counts segments of empty string', function () {
    expect(count_segments(''))->toBe(0);
});

it('counts segments of single segment', function () {
    expect(count_segments('users'))->toBe(1);
});

it('counts segments of multiple segments', function () {
    expect(count_segments('users/posts/123'))->toBe(3);
});

it('counts segments with trailing slash', function () {
    expect(count_segments('users/'))->toBe(1);
});

it('counts segments with leading slash', function () {
    expect(count_segments('/users'))->toBe(1);
});

it('counts segments with both leading and trailing slashes', function () {
    expect(count_segments('/users/'))->toBe(1);
});

it('counts segments of root path', function () {
    expect(count_segments('/'))->toBe(0);
});

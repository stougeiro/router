<?php declare(strict_types=1);

use STDW\Http\Router\Route;

it('gets uri', function () {
    $route = new Route(
        uri: 'users/123',
        map: 'users/{id:num}',
        controller: 'App\Controllers\UserController',
        variables: ['id' => '123'],
    );

    expect($route->getUri())->toBe('users/123');
});

it('gets map', function () {
    $route = new Route(
        uri: 'users/123',
        map: 'users/{id:num}',
        controller: 'App\Controllers\UserController',
    );

    expect($route->getMap())->toBe('users/{id:num}');
});

it('gets controller', function () {
    $route = new Route(
        uri: 'users/123',
        map: 'users/{id:num}',
        controller: 'App\Controllers\UserController',
    );

    expect($route->getController())->toBe('App\Controllers\UserController');
});

it('gets variables', function () {
    $route = new Route(
        uri: 'users/123',
        map: 'users/{id:num}',
        controller: 'App\Controllers\UserController',
        variables: ['id' => '123'],
    );

    expect($route->getVariables())->toBe(['id' => '123']);
});

it('gets empty variables by default', function () {
    $route = new Route(
        uri: 'users',
        map: 'users',
        controller: 'App\Controllers\UserController',
    );

    expect($route->getVariables())->toBe([]);
});

it('gets all data', function () {
    $route = new Route(
        uri: 'users/123',
        map: 'users/{id:num}',
        controller: 'App\Controllers\UserController',
        variables: ['id' => '123'],
    );

    $data = $route->getData();

    expect($data)->toBe([
        'uri' => 'users/123',
        'map' => 'users/{id:num}',
        'controller' => 'App\Controllers\UserController',
        'variables' => ['id' => '123'],
    ]);
});

<?php declare(strict_types=1);

return [
    '/users' => App\Controllers\UserController::class,
    '/users/{id:num}' => [App\Controllers\UserController::class => 'users.show'],
    '/posts/{slug:slug}' => [App\Controllers\PostController::class => 'posts.show'],
    '/api' => [
        '/v1' => [
            '/users' => [
                '/' => App\Api\Controllers\UserController::class,
                '/{id:num}' => App\Api\Controllers\UserController::class,
            ]
        ],
    ],
];

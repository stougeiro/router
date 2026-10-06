![PHP](https://img.shields.io/badge/PHP-%20^8.2-777BB4)
![PHPStan-Level](https://img.shields.io/badge/PHPStan-Level%209-224488)
![Pest-php](https://img.shields.io/badge/Tests-Passed-019733)
![License](https://img.shields.io/badge/License-MIT-777)

# Router

A minimalist, high-performance router for PHP 8.2+ focused exclusively on route resolution. It returns a controller and variables — nothing more. Middleware, request handling, and response generation are intentionally left to dedicated components, keeping routing predictable, fast, and easy to reason about.

## ✨ Features

- **Single Responsibility**  
  Resolves routes and returns controller + variables. No middleware, no request handling, no response generation.

- **Performance-First Design**  
  Indexation by segment count, early return, and pre-compiled regex ensure minimal overhead per request.

- **Dual Cache Support**  
  File and SQLite handlers with configurable associative mode (array or object return).

- **Type Safety**  
  PHPStan Level 9 compliant with strict typing throughout.

- **Declarative Routing**  
  Routes defined exclusively through organized route files, not dynamic mutation.

- **Framework-Agnostic**  
  Works with any request handler or routing engine, without imposing a specific framework or architecture.

- **Modular Architecture**  
  Route collections can be loaded from multiple files, enabling clean separation of modules.

---

## 📦 Installation

Install via Composer:

```bash
composer require stougeiro/router
```

## 🚀 Usage Example

```php
use STDW\Cache\Cache;
use STDW\Cache\CacheConfig;
use STDW\Http\Router\RouteCollection;
use STDW\Http\Router\Router;

$cache = new Cache(new CacheConfig([
    'handler' => 'sqlite',
    'storage' => __DIR__ . '/cache',
    'associative' => true,
]));

$collection = new RouteCollection($cache);
$collection->load(__DIR__ . '/routes.php');

$router = new Router($collection, $cache, true);
$route = $router->match($request);

// $route->getController() → controller class
// $route->getVariables() → ['id' => '123']
```

### Route File Example

```php
<?php

return [
    '/users' => App\Controllers\UserController::class,
    '/users/{id:num}' => [App\Controllers\UserController::class => 'users.show'],
    '/posts/{slug:slug}' => [App\Controllers\PostController::class => 'posts.show'],
];
```

---

## 🧠 Why?

Because routing is one of the core building blocks of any HTTP application — yet most routers mix concerns such as route loading, URL generation, matching logic, request handling, and configuration.

This package takes a different approach:
- RouteCollection is the only entry point for routes.
- Router is a pure matcher.
- Route is the resolved result.
- Middleware and request handling are handled by dedicated components.

By enforcing strict separation of responsibilities, routing becomes:
- predictable
- modular
- cache-friendly
- easy to reason about
- easy to integrate into any framework

The goal is to provide a router that:
- avoids unnecessary abstractions,
- stays predictable and easy to debug,
- works in any environment (CLI, web, microservices),
- and can be extended with custom storage engines when needed.

---

## 🤝 Contributions

Contributions are welcome.
Feel free to open issues or submit pull requests.

<br>

[<img src="https://cdn.buymeacoffee.com/buttons/v2/default-yellow.png" width="170"/>](https://www.buymeacoffee.com/stougeiro)

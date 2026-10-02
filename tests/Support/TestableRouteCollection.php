<?php declare(strict_types=1);

namespace Tests\Support;

use STDW\Http\Router\RouteCollection;

class TestableRouteCollection extends RouteCollection
{
    public function callMap(array $routemaps, string $prefix = ''): void
    {
        $this->map($routemaps, $prefix);
    }

    public function callAdd(string $controller, array $data): void
    {
        $this->add($controller, $data);
    }

    public function callNominate(string $name, array $data): void
    {
        $this->nominate($name, $data);
    }
}

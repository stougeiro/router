<?php declare(strict_types=1);

namespace Tests\Support;

use STDW\Http\Router\Parser\RouteParser;

class TestableRouteParser extends RouteParser
{
    public function callValidate(string $uri): string
    {
        return $this->validate($uri);
    }
}

<?php declare(strict_types=1);

    namespace STDW\Http\Router;

    use STDW\Contract\Http\Router\RouteInterface;


    class Route implements RouteInterface
    {
        public function getController(): string
        {
            throw new \Exception('Not implemented');
        }

        public function getVariables(): array
        {
            throw new \Exception('Not implemented');
        }
    }

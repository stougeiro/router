<?php declare(strict_types=1);

    namespace STDW\Http\Router;

    use STDW\Contract\Http\Router\RouteInterface;


    final class Route implements RouteInterface
    {
        /**
         * @param string $uri 
         * @param string $map 
         * @param string $controller 
         * @param array<string, mixed> $variables 
         * @return void 
         */
        public function __construct(
            protected string $uri,
            protected string $map,
            protected string $controller,
            protected array $variables = [],
        ) {}


        /** @return string
         */
        public function getUri(): string
        {
            return $this->uri;
        }

        /** @return string
         */
        public function getMap(): string
        {
            return $this->map;
        }

        /** @return string
         */
        public function getController(): string
        {
            return $this->controller;
        }

        /** @return array<string, mixed>
         */
        public function getVariables(): array
        {
            return $this->variables;
        }

        /**
         * @return array{
         *   uri: string,
         *   map: string,
         *   controller: string,
         *   variables: array<string, mixed>
         * }
         */
        public function getData(): array
        {
            return [
                'uri' => $this->uri,
                'map' => $this->map,
                'controller' => $this->controller,
                'variables' => $this->variables,
            ];
        }
    }

<?php declare(strict_types=1);

    namespace STDW\Http\Router;

    use STDW\Contract\Http\Router\RouteInterface;


    class Route implements RouteInterface
    {
        public function __construct(
            protected string $controller,
            protected array $variables = [],
        ) {}


        /** @return string
         */
        public function getController(): string
        {
            return $this->controller;
        }

        /** @return array<string, string>
         */
        public function getVariables(): array
        {
            return $this->variables;
        }
    }

<?php declare(strict_types=1);

    namespace STDW\Http\Router\Exception;

    use Exception;


    class RouterException extends Exception
    {
        /**
         * @param string $type 
         * @param array<string> $registered 
         * @return RouterException 
         */
        public static function unknownPlaceholder(string $type, array $registered): self
        {
            $list = implode(', ', $registered);

            return new self("Unknown placeholder '{$type}'. Registered placeholders: {$list}");
        }

        /**
         * @param string $name 
         * @return RouterException 
         */
        public static function invalidPlaceholderName(string $name): self
        {
            return new self("Invalid placeholder name '{$name}'. Placeholder names must match: [a-z0-9.]");
        }

        /**
         * @param string $file 
         * @return RouterException 
         */
        public static function fileNotFound(string $file): self
        {
            return new self("Routes file '{$file}' not found");
        }

        /**
         * @param string $file 
         * @return RouterException 
         */
        public static function fileContentNotValid(string $file): self
        {
            return new self("Routes file '{$file}' must return an array");
        }

        /**
         * @param string $route 
         * @return RouterException 
         */
        public static function routeNotMapped(string $route): self
        {
            return new self("Route '{$route}' cannot be mapped");
        }

        /**
         * @param string $name 
         * @return RouterException 
         */
        public static function invalidRouteName(string $name): self
        {
            return new self("Invalid route name '{$name}'. Route names must match: [a-z0-9.]");
        }

        /**
         * @param string $routemap 
         * @param string $name 
         * @return RouterException 
         */
        public static function routeNameAlreadyRegistered(string $routemap, string $name): self
        {
            return new self("Route '{$routemap}' cannot be named by already registered '{$name}'");
        }

        /**
         * @param string $name 
         * @return RouterException 
         */
        public static function namedRouteNotFound(string $name): self
        {
            return new self("Named route '{$name}' not found");
        }

        /**
         * @param string $var 
         * @param string $route 
         * @return RouterException 
         */
        public static function missingVariable(string $var, string $route): self
        {
            return new self("Variable '{$var}' is required for route '{$route}'");
        }
    }

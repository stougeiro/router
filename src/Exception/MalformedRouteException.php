<?php declare(strict_types=1);

    namespace STDW\Http\Router\Exception;

    use RuntimeException;


    class MalformedRouteException extends RuntimeException
    {
        /**
         * @param string $uri 
         * @return MalformedRouteException 
         */
        public static function duplicateSlashes(string $uri): self
        {
            return new self("URI contains duplicate slashes: '{$uri}'");
        }

        /**
         * @param string $uri 
         * @return MalformedRouteException 
         */
        public static function invalidCharacters(string $uri): self
        {
            return new self("Route contains invalid characters: '{$uri}'. Allowed: a-z, A-Z, 0-9, -, _, /, {, }, :, .");
        }
    }

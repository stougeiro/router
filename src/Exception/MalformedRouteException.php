<?php declare(strict_types=1);

    namespace STDW\Http\Router\Exception;

    use RuntimeException;


    class MalformedRouteException extends RuntimeException
    {
        /**
         * @param string $uri 
         * @return MalformedRouteException 
         */
        public static function whitespace(string $uri): self
        {
            return new self("URI contains whitespace: '{$uri}'");
        }

        /**
         * @param string $uri 
         * @return MalformedRouteException 
         */
        public static function duplicateSlashes(string $uri): self
        {
            return new self("URI contains duplicate slashes: '{$uri}'");
        }
    }

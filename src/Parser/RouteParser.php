<?php declare(strict_types=1);

    namespace STDW\Http\Router\Parser;

    use STDW\Http\Router\Exception\MalformedRouteException;
    use STDW\Http\Router\Placeholder\PlaceholderRegistry;
    use function STDW\Http\Router\Helper\count_segments;


    class RouteParser
    {
        public function __construct(
            private PlaceholderRegistry $placeholders,
        ) {}


        /**
         * @return array{
         *   map: string,
         *   segments: int,
         *   route: string,
         *   variables: bool,
         * }
         */
        public function parse(string $pattern): array
        {
            $variables = false;
            $map = $this->validate($pattern);
            $segments = count_segments($map);

            $route = preg_replace_callback('/\{(\w+):(\w+)\}/', function ($matches) use (&$variables): string {
                $variables = true;
                $param = $matches[1];
                $type = $matches[2];
                $regex = $this->placeholders->replace($type);

                return "(?P<{$param}>{$regex})";
            }, $map);

            $route = '/^'. str_replace('/', '\/', $route) .'$/';

            return compact('variables', 'map', 'segments', 'route');
        }

        /**
         * @param string $uri 
         * @return string 
         * @throws MalformedRouteException 
         */
        public function validate(string $uri): string
        {
            if (preg_match('/\s/', $uri)) {
                throw MalformedRouteException::whitespace($uri);
            }

            if (str_contains($uri, '//')) {
                throw MalformedRouteException::duplicateSlashes($uri);
            }

            return trim($uri, '/');
        }
    }

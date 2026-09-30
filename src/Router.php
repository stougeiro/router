<?php declare(strict_types=1);

    namespace STDW\Http\Router;

    use STDW\Contract\Http\Router\RouterInterface;
    use STDW\Contract\Http\Router\RouteInterface;
    use STDW\Contract\Http\RequestInterface;
    use STDW\Http\Router\Parser\RouteParser;


    class Router implements RouterInterface
    {
        public function __construct(
            protected RouteParser $parser,
            protected RouteCollection $collection,
        ) {}


        public function match(RequestInterface $request): ?RouteInterface
        {
            $path = $request->getUri()->getPath();
            $segments = $this->parser->countSegments($path);
            $collection = $this->collection->all();

            if ( ! isset($collection[$segments])) {
                return null;
            }

            $static = $collection[$segments]['static'];
            $dynamic = $collection[$segments]['dynamic'];

            if (isset($group['static'][$path])) {
                return new Route(
                    controller: $routes[$path]['controller'],
                );
            }

            foreach ($routes[$segments] as $route) {
                if (preg_match($route['route'], $path, $variables)) {
                    $vars = array_filter($variables, 'is_string', ARRAY_FILTER_USE_KEY);

                    return new Route(
                        controller: $route['controller'],
                        variables: $vars,
                    );
                }
            }

            return null;
        }
    }

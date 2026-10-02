<?php declare(strict_types=1);

    namespace STDW\Http\Router;

    use STDW\Contract\Http\Router\RouterInterface;
    use STDW\Contract\Http\Router\RouteInterface;
    use STDW\Contract\Http\Router\RouteCollectionInterface;
    use STDW\Contract\Cache\CacheInterface;
    use STDW\Contract\Http\RequestInterface;
    use function STDW\Http\Router\Helper\count_segments;


    class Router implements RouterInterface
    {
        /**
         * @var array{
         *   'routes': array<int, array<string, array<string, array<string, string>>>>,
         *   'names': array<string, string>
         * } $data
         */
        protected array $data;


        /**
         * @param RouteCollectionInterface $collection
         * @param CacheInterface $cache
         * @param bool $withCache
         * @return void
         */
        public function __construct(
            protected RouteCollectionInterface $collection,
            protected CacheInterface $cache,
            protected bool $withCache = false,
        ) {
            if ( ! $this->withCache)
            {
                $this->cache->delete('routes');

                /**
                 * @var array{
                 *   'routes': array<int, array<string, array<string, array<string, string>>>>,
                 *   'names': array<string, string>
                 * } $resultset
                 */
                $resultset = $this->collection->all();
                $this->data = $resultset;

                return;
            }

            if( ! $this->cache->has('routes')) {
                /**
                 * @var array{
                 *   'routes': array<int, array<string, array<string, array<string, string>>>>,
                 *   'names': array<string, string>
                 * } $resultset
                 */
                $resultset = $this->collection->all();
                $this->data = $resultset;

                $this->cache->set('routes', $resultset);

                return;
            }


            /**
             * @var array{
             *   'routes': array<int, array<string, array<string, array<string, string>>>>,
             *   'names': array<string, string>
             * } $resultset
             */
            $resultset = $this->cache->get('routes');

            $this->data = $resultset;
        }


        /**
         * @param RequestInterface $request
         * @return null|RouteInterface
         */
        public function match(RequestInterface $request): ?RouteInterface
        {
            $path = $request->getUri()->getPath();
            $path = trim($path, '/');
            $segments = count_segments($path);

            /** @var array<int, array<string, array<string, array<string, string>>>> $routes
             */
            $routes = $this->data['routes'];

            if ( ! isset($routes[$segments])) {
                return null;
            }


            /** @var array<string, array<string, array<string, string>>> $group
             */
            $group = $routes[$segments];

            /** @var array<string, array<string, string>> $static
             */
            $static = $group['static'] ?? [];

            /** @var array<string, array<string, string>> $dynamic
             */
            $dynamic = $group['dynamic'] ?? [];

            if (isset($static[$path])) {
                /** @var array{ controller: string, route: string, name?: string } $resource
                 */
                $resource = $static[$path];

                return new Route(
                    uri: $path,
                    map: $path,
                    controller: $resource['controller'],
                );
            }

            /** @var array{ controller: string, route: string, name?: string } $resource
             */
            foreach ($dynamic as $map => $resource) {
                if (preg_match($resource['route'], $path, $variables)) {
                    $vars = [];

                    foreach ($variables as $key => $value) {
                        if (is_string($key)) {
                            $vars[$key] = $value;
                        }
                    }

                    return new Route(
                        uri: $path,
                        map: $map,
                        controller: $resource['controller'],
                        variables: $vars,
                    );
                }
            }

            return null;
        }
    }

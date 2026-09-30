<?php declare(strict_types=1);

    namespace STDW\Http\Router;

    use STDW\Contract\Cache\CacheInterface;
    use STDW\Contract\Http\Router\RouteCollectionInterface;
    use STDW\Http\Router\Parser\RouteParser;
    use STDW\Http\Router\Exception\RouterException;


    class RouteCollection implements RouteCollectionInterface
    {
        /** @var array
         */
        protected array $routes = [];

        /** @var array
         */
        protected array $names = [];


        public function __construct(
            protected RouteParser $parser,
            protected CacheInterface $cache,
        ) {}


        /**
         * @param string $file
         * @return void
         * @throws RouterException
         */
        public function load(string $file): void
        {
            if ( ! file_exists($file)) {
                throw new RouterException("router: file {$file} not found");
            }

            $routes = include $file;

            if ( ! is_array($routes)) {
                throw new RouterException("router: file {$file} must return an array");
            }

            $this->map($routes);
        }

        /**
         * @param string $name
         * @param array<string, mixed> $vars
         * @return string
         * @throws RouterException
         */
        public function get(string $name, array $vars = []): string
        {
            if ( ! isset($this->names[$name])) {
                throw new RouterException("router: route '{$name}' not exists");
            }

            $route = preg_replace_callback('/\{([^}]+)\}/', function ($match) use ($name, $vars) {
                $varName = explode(':', $match[1])[0];

                if ( ! isset($vars[$varName])) {
                    throw new RouterException("router: '{$varName}' is missing for route '{$name}' in '{$match[0]}'");
                }

                return (string) $vars[$varName];
            }, $this->names[$name]);

            return $route;
        }

        /** @return array
         */
        public function all(): array
        {
            return $this->routes;
        }


        /**
         * @param array $routemaps
         * @param string $prefix
         * @return void
         * @throws RouterException
         */
        protected function map(array $routemaps, string $prefix = ''): void
        {
            foreach ($routemaps as $route => $mix) {
                if (
                       is_string($mix)
                    && is_file($mix)
                ) {
                    $this->add($prefix .'/'. $route, $mix);
                }

                elseif (
                       is_array($mix)
                    && count($mix) === 1
                    && is_file(key($mix))
                ) {
                    $this->add($prefix .'/'. $route, key($mix));
                    $this->nominate(current($mix), $prefix .'/'. $route);
                }

                elseif (
                    is_array($mix)
                ) {
                    $this->map($mix, $prefix .'/'. $route);
                }

                else {
                    throw new RouterException('Route "'. $route .'" cannot be mapped.');
                }
            }
        }

        /**
         * @param string $uri
         * @param string $controller
         * @return void
         */
        protected function add(string $uri, string $controller): void
        {
            $parsed = $this->parser->parse($uri);

            $this->routes[$parsed['segments']][$parsed['map']] = [
                'route' => $parsed['route'],
                'controller' => $controller,
            ];
        }

        /**
         * @param string $name
         * @param string $route
         * @return void
         * @throws RouterException
         */
        protected function nominate(string $name, string $route): void
        {
            if ( ! preg_match('/^[a-z0-9.]+$/', $name)) {
                throw new RouterException("Route name '{$name}' is not valid");
            }

            $parsed = $this->parser->parse($route);
            $routemap = $this->parser->validate('/' . $parsed['routemap']);

            if ( ! str_starts_with($routemap, '/')) {
                $routemap = '/' . $routemap;
            }

            if (isset($this->names[$name])) {
                throw new RouterException("Route '{$routemap}' cannot be named by already registered '{$name}'");
            }

            $this->names[$name] = $routemap;

            if (isset($this->routes[$parsed['parts']][$parsed['routemap']])) {
                $this->routes[$parsed['parts']][$parsed['routemap']]['name'] = $name;
            }
        }
    }

<?php declare(strict_types=1);

    namespace STDW\Http\Router;

    use STDW\Contract\Cache\CacheInterface;
    use STDW\Contract\Http\Router\RouteCollectionInterface;
    use STDW\Http\Router\Parser\RouteParser;
    use STDW\Http\Router\Exception\RouterException;
    use STDW\Http\Router\Cache\CacheableTrait;
    use STDW\Support\Str;


    class RouteCollection implements RouteCollectionInterface
    {
        use CacheableTrait;


        /** @var RouteParser
         */
        protected RouteParser $parser;

        /** @var array<int, array<string, array<string, array<string, mixed>>>>
         */
        protected array $routes = [];

        /** @var array<string, string>
         */
        protected array $names = [];


        /**
         * @param CacheInterface $cache
         * @return void
         */
        public function __construct(
            protected CacheInterface $cache,
        ) {
            $this->parser = new RouteParser();
        }


        /**
         * @param string $file
         * @return void
         * @throws RouterException
         */
        public function load(string $file): void
        {
            if ($this->hasRoutesInCache()) {
                /**
                 * @var array{
                 *   routes: array<int, array<string, array<string, array<string, mixed>>>>,
                 *   names: array<string, string>
                 * } $cached
                 */
                $cached = $this->getRoutesFromCache();

                $this->routes = $cached['routes'];
                $this->names = $cached['names'];

                return;
            }

            if ( ! file_exists($file)) {
                throw RouterException::fileNotFound($file);
            }

            /** @var mixed $routes
             */
            $routes = include $file;

            if ( ! is_array($routes)) {
                throw RouterException::fileContentNotValid($file);
            }

            /** @var array<string, mixed> $routes
             */
            $this->map($routes);
        }

        /**
         * @param string $name
         * @param array<string, string> $vars
         * @return string
         * @throws RouterException
         */
        public function generate(string $name, array $vars = []): string
        {
            if ( ! isset($this->names[$name])) {
                throw RouterException::namedRouteNotFound($name);
            }

            /** @var string $map
             */
            $map = $this->names[$name];

            $route = preg_replace_callback('/\{(\w+):(\w+)\}/', function ($match) use ($map, $name, $vars) {
                $variable = $match[1];

                if ( ! isset($vars[$variable])) {
                    throw RouterException::missingVariable($variable, "$name: $map");
                }

                return (string) $vars[$variable];
            },  $map);

            return (string) $route;
        }

        /**
         * @return array{
         *   'routes': array<int, array<string, array<string, array<string, mixed>>>>,
         *   'names': array<string, string>
         * }
         */
        public function all(): array
        {
            return [
                'routes' => $this->routes,
                'names' => $this->names,
            ];
        }


        /**
         * @param array<string, mixed> $routemaps
         * @param string $prefix
         * @return void
         * @throws RouterException
         */
        protected function map(array $routemaps, string $prefix = ''): void
        {
            foreach ($routemaps as $route => $mix) {
                if (is_string($mix) && Str::isFqcn($mix)) {

                    $parsed = $this->parser->parse($prefix.$route);
                    $type = $parsed['variables'] ? 'dynamic' : 'static';

                    $this->add($mix, [
                        'map' => $parsed['map'],
                        'segments' => $parsed['segments'],
                        'route' => $parsed['route'],
                        'type' => $type,
                    ]);

                } elseif (is_array($mix) && count($mix) === 1 && Str::isFqcn( key($mix))) {

                    $parsed = $this->parser->parse($prefix.$route);
                    $type = $parsed['variables'] ? 'dynamic' : 'static';
                    $controller = key($mix);

                    $this->add($controller, [
                        'map' => $parsed['map'],
                        'segments' => $parsed['segments'],
                        'route' => $parsed['route'],
                        'type' => $type,
                    ]);

                    /** @var string $name
                     */
                    $name = $mix[$controller];

                    $this->nominate($name, [
                        'map' => $parsed['map'],
                        'segments' => $parsed['segments'],
                        'route' => $parsed['route'],
                        'type' => $type,
                    ]);

                } elseif (is_array($mix)) {
                    /** @var array<string, mixed> $mix
                     */
                    $this->map($mix, $prefix.$route);
                } else {
                    throw RouterException::routeNotMapped($prefix.$route);
                }
            }
        }

        /**
         * @param string $controller
         * @param array{
         *   map: string,
         *   segments: int,
         *   route: string,
         *   type: string,
         * } $data
         * @return void
         */
        protected function add(string $controller, array $data): void
        {
            $this->routes[$data['segments']][$data['type']][$data['map']] = [
                'controller' => $controller,
                'route' => $data['route'],
            ];
        }

        /**
         * @param string $name
         * @param array{
         *   map: string,
         *   segments: int,
         *   route: string,
         *   type: string,
         * } $data
         * @return void
         * @throws RouterException
         */
        protected function nominate(string $name, array $data): void
        {
            if ( ! preg_match('/^[a-z0-9.]+$/', $name)) {
                throw RouterException::invalidRouteName($name);
            }

            if (isset($this->names[$name])) {
                throw RouterException::routeNameAlreadyRegistered($data['map'], $name);
            }

            $this->names[$name] = $data['map'];

            if (isset($this->routes[$data['segments']][$data['type']][$data['map']])) {
                $this->routes[$data['segments']][$data['type']][$data['map']]['name'] = $name;
            }
        }
    }

<?php declare(strict_types=1);

    namespace STDW\Http\Router;

    use STDW\Contract\Http\Router\RouteCollectionInterface;
    use STDW\Http\Router\Spec\CacheableCollectionInterface;


    class RouteCollection implements RouteCollectionInterface, CacheableCollectionInterface
    {
        /**
         * @param string $file
         * @return void
         */
        public function load(string $file): void
        {

        }

        /**
         * @param string $name
         * @param array<string, mixed> $vars
         * @return string
         */
        public function get(string $name, array $vars = []): string
        {
            throw new \Exception('Not implemented');
        }

        /** @return array<string, string>
         */
        public function all(): array
        {
            throw new \Exception('Not implemented');
        }

        /**
         * @param array<string|int, string> $collection
         * @return void
         */
        public function fromArray(array $collection): void
        {
            throw new \Exception('Not implemented');
        }

        /** @return array
         */
        public function toArray(): array
        {
            throw new \Exception('Not implemented');
        }
    }

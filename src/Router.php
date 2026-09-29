<?php declare(strict_types=1);

    namespace STDW\Http\Router;

    use STDW\Contract\Http\Router\RouterInterface;
    use STDW\Contract\Http\Router\RouteInterface;
    use STDW\Contract\Http\RequestInterface;


    class Router implements RouterInterface
    {
        /**
         * @param RequestInterface $request 
         * @return null|RouteInterface 
         */
        public function match(RequestInterface $request): ?RouteInterface
        {
            throw new \Exception('Not implemented');
        }
    }

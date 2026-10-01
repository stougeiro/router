<?php declare(strict_types=1);

    namespace STDW\Http\Router\Helper;


    /**
     * @param string $uri 
     * @return int 
     */
    function count_segments(string $uri): int
    {
        $uri = trim($uri, '/');
        $segments = explode('/', $uri);

        return ($uri === '') ? 0 : count($segments);
    }

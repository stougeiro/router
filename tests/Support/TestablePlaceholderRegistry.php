<?php declare(strict_types=1);

namespace Tests\Support;

use STDW\Http\Router\Parser\PlaceholderRegistry;

class TestablePlaceholderRegistry extends PlaceholderRegistry
{
    public function callDefaults(): array
    {
        return $this->defaults();
    }
}

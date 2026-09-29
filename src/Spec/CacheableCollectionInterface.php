<?php declare(strict_types=1);

    namespace STDW\Http\Router\Spec;


    interface CacheableCollectionInterface
    {
        /**
         * @param array<string> $collection 
         * @return void 
         */
        public function fromArray(array $collection): void;

        /** @return array 
         */
        public function toArray(): array;
    }

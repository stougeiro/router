<?php declare(strict_types=1);

    namespace STDW\Http\Router\Parser;

    use STDW\Http\Router\Exception\RouterException;


    class PlaceholderRegistry
    {
        /** @var array<string, string>
         */
        protected array $placeholders = [];


        /**
         * @param array<string, string> $placeholders 
         * @return void 
         */
        public function __construct(array $placeholders = [])
        {
            $this->placeholders = array_merge($this->defaults(), $placeholders);
        }


        /**
         * @param string $name
         * @param string $regex
         * @return void
         */
        public function register(string $name, string $regex): void
        {
            if ( ! preg_match('/^[a-z0-9.]+$/', $name)) {
                throw RouterException::invalidPlaceholderName($name);
            }

            $this->placeholders[$name] = $regex;
        }

        /**
         * @param string $type
         * @return string
         * @throws RouterException
         */
        public function replace(string $type): string
        {
            if ( ! isset($this->placeholders[$type])) {
                throw RouterException::unknownPlaceholder($type, array_keys($this->placeholders));
            }

            return $this->placeholders[$type];
        }

        /** @return array<string, string>
         */
        public function all(): array
        {
            return $this->placeholders;
        }


        /** @return array<string, string>
         */
        protected function defaults(): array
        {
            return [
                'any'   => '[^/]+',
                'num'   => '[0-9]+',
                'word'  => '[a-zA-Z]+',
                'slug'  => '[a-z0-9-]+',
                'color' => '(?:[a-fA-F0-9]{3}|[a-fA-F0-9]{6})',
                'year'  => '(?:19|20)[0-9]{2}',
                'month' => '(?:0[1-9]|1[0-2])',
                'day'   => '(?:0[1-9]|[12][0-9]|3[01])',
                'token' => '[a-f0-9]{32,128}',
            ];
        }
    }

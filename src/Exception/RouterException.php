<?php declare(strict_types=1);

    namespace STDW\Http\Router\Exception;

    use Exception;


    class RouterException extends Exception
    {
        /**
         * @param string $type 
         * @param list<string> $registered 
         * @return RouterException 
         */
        public static function unknownPlaceholder(string $type, array $registered): self
        {
            $list = implode(', ', $registered);

            return new self("Unknown placeholder '{$type}'. Registered placeholders: {$list}");
        }

        /**
         * @param string $name 
         * @return RouterException 
         */
        public static function invalidPlaceholderName(string $name): self
        {
            return new self("Invalid placeholder name '{$name}'. Placeholder names must match: [a-z0-9.]");
        }
    }

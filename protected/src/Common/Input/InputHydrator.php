<?php
declare (strict_types = 1);
/**
 * This file is part of Scaleum Framework.
 *
 * (C) 2009-2025 Maxim Kirichenko <kirichenko.maxim@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Application\Common\Input;

use Scaleum\Stdlib\Helpers\ArrayHelper;

/**
 * InputHydrator
 *
 * @author Maxim Kirichenko <kirichenko.maxim@gmail.com>
 */
class InputHydrator {
    protected static function ensureHydratable(string $class): void {
        if (! is_subclass_of($class, HydratableInputInterface::class)) {
            throw new \InvalidArgumentException("Класс $class не реализует HydratableInputInterface");
        }
    }

    public static function validate(array $haystack, string $class): void {
        self::ensureHydratable($class);
        $required = call_user_func([$class, 'getRequiredFields']);
        foreach ($required as $key) {
            if (! array_key_exists($key, $haystack)) {
                throw new \InvalidArgumentException("Отсутствует обязательное поле: $key");
            }
        }
    }

    public static function map(array $haystack, string $class): array {
        self::ensureHydratable($class);
        $map    = call_user_func([$class, 'getFieldMap']);
        $result = [];

        if(!ArrayHelper::isAssociative($haystack)){
            foreach($haystack as $data){
                $result[] = self::map($data, $class);
            }
            return $result;
        }

        foreach ($map as $property => $definition) {
            $data = $haystack[$property] ?? null;
            if ($data === null) {
                continue;
            }

            if ($definition !== true && is_subclass_of($definition, HydratableInputInterface::class) && is_array($data)) {
                $result[$property] = self::map($data, $definition);
            } else {
                $result[$property] = $data;
            }
        }

        return $result;
    }
    public static function prepare(array $haystack, string $class): array {
        self::validate($haystack, $class);
        return self::map($haystack, $class);
    }

    // public static function export(array $haystack, string $class): array {
    //     $mapped = self::prepare($haystack, $class);
    //     return call_user_func([$class, 'exportMapped'], $mapped);
    // }

    // public static function exportJson(array $data, string $class): string {
    //     $mapped = self::prepare($data, $class);
    //     return call_user_func([$class, 'exportJson'], $mapped);
    // }
}
/** End of InputHydrator **/
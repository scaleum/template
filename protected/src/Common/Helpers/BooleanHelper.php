<?php

declare (strict_types = 1);
/**
 * This file is part of Scaleum Application.
 *
 * (C) 2009-2024 Maxim Kirichenko <kirichenko.maxim@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Common\Helpers;

/**
 * BooleanHelper
 *
 * @author Maxim Kirichenko <kirichenko.maxim@gmail.com>
 */
class BooleanHelper {
    public const TRUE_VALUES  = ['true', '1', 'yes', 'on', 'enable', 'enabled', TRUE];
    public const FALSE_VALUES = ['false', '0', 'no', 'off', 'none', 'disable', 'disabled', FALSE, null];

    public static function toBoolean(mixed $value) {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            $value = strtolower($value);
            if (in_array($value, self::TRUE_VALUES)) {
                return true;
            }

            if (in_array($value, self::FALSE_VALUES)) {
                return false;
            }
        }

        if (is_numeric($value)) {
            return (bool) $value;
        }

        return false;
    }

    public static function toInteger(mixed $value) {
        return self::toBoolean($value) ? 1 : 0;
    }

    public static function toString(mixed $value) {
        return self::toBoolean($value) ? 'true' : 'false';
    }

    public static function isLogical(mixed $value): bool {
        return in_array($value, self::TRUE_VALUES) || in_array($value, self::FALSE_VALUES);
    }
}
/** End of BooleanHelper **/

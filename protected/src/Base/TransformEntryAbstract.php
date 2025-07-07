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

namespace Application\Base;

use Application\Contracts\TransformEntryInterface;
use Scaleum\Stdlib\Helpers\StringCaseHelper;

abstract class TransformEntryAbstract implements TransformEntryInterface {
    /**
     * Array of required data params for type
     *
     * @var array
     */
    protected static array $requiredParams = [];

    /**
     * Map of input data
     *
     * @var array
     */
    protected static array $map = [];

    /**
     * @return array
     */
    public static function getRequiredParams(): array {
        return static::$requiredParams;
    }

    public static function getMap():array {
        return static::$map;
    }

    public static function validate(array $data):bool {
        if (count(array_intersect_key(array_flip(static::getRequiredParams()), $data)) === count(static::getRequiredParams())) {
            return true;
        }

        throw new \Scaleum\Stdlib\Exceptions\EPropertyError('Required properties of the object are not satisfactory');
    }

    public function map($data):void {
        foreach (static::getMap() as $key => $item) {
            if (isset($data[$key]) && (! is_array($data[$key]) || (is_array($data[$key]) && ! empty($data[$key])))) {
                $method = 'set' . StringCaseHelper::camelize($key);
                if (method_exists($this, $method)) {
                    if ($item === true) {
                        $this->$method($data[$key]);
                    } else {
                        $this->$method($item::fromInput($data[$key]));
                    }
                }
            }
        }
    }
}

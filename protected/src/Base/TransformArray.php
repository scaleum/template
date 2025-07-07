<?php
declare(strict_types=1);
/**
 * This file is part of Scaleum Framework.
 *
 * (C) 2009-2025 Maxim Kirichenko <kirichenko.maxim@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Base;


class TransformArray extends TransformEntryAbstract
{
    public function toArray()
    {
        $output = [];
        foreach (static::getMap() as $key => $item) {
            $property = $key;
            if (!is_null( $this->$property )) {
                if (is_array( $this->$property )) {
                    $output[$key] = array_map(
                      function ($item) {
                          return ($item instanceof TransformArray) ? $item->toArray() : $item;
                      },
                      $this->$property
                    );
                } else {
                    $output[$key] = ($item === true) ? $this->$property : (($this->$property instanceof TransformArray) ? $this->$property->toArray() : $this->$property);
                }
            }
        }

        return $output;
    }

    public static function fromInput(mixed $data)
    {
        if ($data === true || !is_array( $data )) {
            return true;
        }

        self::validate( $data );
        $instance = new static();
        $instance->map( $data );

        return $instance;
    }
}

/* End of file TransformArray.php */

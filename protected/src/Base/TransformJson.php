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

use Scaleum\Stdlib\Helpers\JsonHelper;

class TransformJson extends TransformArray
{
    public function toJson()
    {
        return JsonHelper::encode(parent::toArray());
    }

    public static function fromInput($data)
    {
        if (JsonHelper::isJson( $data )) {
            return parent::fromInput( json_decode( $data, true ) );
        }

        return true;
    }
}

/* End of file TransformJson.php */

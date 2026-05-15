<?php
declare(strict_types=1);
/**
 * This file is part of Scaleum Application.
 *
 * (C) 2009-2025 Maxim Kirichenko <kirichenko.maxim@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Application\Common\Helpers;


/**
 * PackerHelper
 *
 * @author Maxim Kirichenko <kirichenko.maxim@gmail.com>
 */
class PackerHelper
{
    public static function pack(mixed $data): string {
        return base64_encode(gzcompress(serialize($data)));
    }

    public static function unpack(string $data): mixed {
        return unserialize(gzuncompress(base64_decode($data)));
    }
}
/** End of PackerHelper **/
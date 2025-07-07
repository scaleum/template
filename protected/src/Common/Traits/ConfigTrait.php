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

namespace Application\Common\Traits;

use Scaleum\Config\Config;
use Scaleum\Services\ServiceLocator;
use Scaleum\Stdlib\Exceptions\ERuntimeError;

/**
 * ConfigTrait
 *
 * @author Maxim Kirichenko <kirichenko.maxim@gmail.com>
 */
trait ConfigTrait
{
    public function getConfig(): Config {
        $config = ServiceLocator::get('config');
        if (! $config instanceof Config) {
            throw new ERuntimeError('Config service is not set or invalid');
        }
        return $config;
    }
}
/** End of ConfigTrait **/
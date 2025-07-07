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

use Scaleum\Services\ServiceLocator;
use Scaleum\Stdlib\Exceptions\ERuntimeError;
use Scaleum\Storages\PDO\Database;

/**
 * DatabaseTrait
 *
 * @author Maxim Kirichenko <kirichenko.maxim@gmail.com>
 */
trait DatabaseTrait
{
    public function getDatabase(string $alias = 'db'): Database
    {
        $db = ServiceLocator::get($alias);
        if (! $db instanceof Database) {
            throw new ERuntimeError('Database service is not set or invalid');
        }
        return $db;
    }
}
/** End of DatabaseTrait **/
